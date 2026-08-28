<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderComplaint;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderComplaintController extends Controller
{
    /**
     * TAMPILKAN FORM PENGAJUAN KOMPLAIN PELANGGAN
     */
    public function showForm($order_number)
    {
        $order = Order::with(['items.product', 'complaints'])->where('order_number', $order_number)->firstOrFail();

        // PROTEKSI KETAT: Jika pesanan belum lunas, tidak dapat mengakses form komplain
        if ($order->payment_status !== 'paid') {
            return redirect()->route('order.pay', $order->order_number)
                             ->with('error', 'Pesanan ini belum dibayar. Layanan komplain & garansi toko hanya dapat diakses untuk pesanan yang telah lunas.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('order.track', $order->order_number)
                             ->with('error', 'Pesanan ini telah dibatalkan.');
        }

        return Inertia::render('Online/Complaint', [
            'order'        => $order,
            'order_number' => $order_number,
        ]);
    }

    /**
     * PROSES SIMPAN KOMPLAIN PELANGGAN BESERTA BUKTI FOTO & VIDEO
     */
    public function store(Request $request, $order_number)
    {
        $order = Order::with('items')->where('order_number', $order_number)->firstOrFail();

        // PROTEKSI KETAT: Validasi status lunas di sisi server
        if ($order->payment_status !== 'paid') {
            return redirect()->route('order.pay', $order->order_number)
                             ->with('error', 'Pesanan belum lunas dibayar. Anda tidak dapat mengajukan komplain.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('order.track', $order->order_number)
                             ->with('error', 'Pesanan ini telah dibatalkan.');
        }

        $request->validate([
            'reason'            => 'required|string|max:255',
            'description'       => 'required|string|min:10|max:2000',
            'expected_solution' => 'required|string|max:255',
            'photos'            => 'required|array|min:1|max:10',
            'photos.*'          => 'file|mimes:jpeg,png,jpg,webp,gif|max:25600', // Max 25MB per/total image
            'video'             => 'nullable|file|mimes:mp4,mov,avi,webm,mkv,3gp|max:15360', // Max 15MB
        ], [
            'reason.required'            => 'Pilih alasan pengajuan komplain.',
            'description.required'       => 'Jelaskan secara rinci kendala barang yang Anda terima.',
            'description.min'            => 'Keterangan kendala minimal 10 karakter.',
            'expected_solution.required' => 'Pilih solusi yang Anda harapkan.',
            'photos.required'            => 'Wajib mengunggah minimal 1 foto bukti kendala barang.',
            'photos.*.max'               => 'Ukuran masing-masing foto tidak boleh melebihi 25MB.',
            'video.max'                  => 'Ukuran video bukti tidak boleh melebihi 15MB.',
            'video.mimes'                => 'Format video harus berupa MP4, MOV, AVI, WEBM, MKV, atau 3GP.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Upload Foto-Foto Bukti (Bisa lebih dari 1 gambar)
            $uploadedPhotos = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photoFile) {
                    $filename = 'complaint_' . $order->order_number . '_' . Str::random(8) . '.' . $photoFile->getClientOriginalExtension();
                    $path = $photoFile->storeAs('complaints/photos', $filename, 'public');
                    $uploadedPhotos[] = $path;
                }
            }

            // 2. Upload Video Bukti Unboxing / Kerusakan (Max 15MB)
            $uploadedVideo = null;
            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');
                $videoFilename = 'video_' . $order->order_number . '_' . Str::random(8) . '.' . $videoFile->getClientOriginalExtension();
                $uploadedVideo = $videoFile->storeAs('complaints/videos', $videoFilename, 'public');
            }

            // 3. Simpan ke Database
            OrderComplaint::create([
                'order_id'          => $order->id,
                'order_number'      => $order->order_number,
                'customer_name'     => $order->customer_name,
                'customer_phone'    => $order->customer_phone,
                'reason'            => $request->reason,
                'description'       => $request->description,
                'expected_solution' => $request->expected_solution,
                'photos'            => $uploadedPhotos,
                'video'             => $uploadedVideo,
                'status'            => 'pending',
            ]);

            DB::commit();

            return redirect()->route('order.complaint.show', $order->order_number)
                             ->with('success', 'Komplain Anda berhasil dikirim! Tim kasir/admin toko kami akan segera meninjau dan menghubungi Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal simpan komplain pesanan {$order_number}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengajukan komplain: ' . $e->getMessage());
        }
    }

    /**
     * UPDATE STATUS KOMPLAIN OLEH ADMIN/KASIR
     */
    public function adminUpdateStatus(Request $request, $id)
    {
        $complaint = OrderComplaint::with('order')->findOrFail($id);

        $request->validate([
            'status'      => 'required|in:pending,reviewed,approved,rejected,resolved',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $complaint->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes ?: $complaint->admin_notes,
            'resolved_by' => Auth::id(),
            'resolved_at' => ($request->status === 'resolved' || $request->status === 'approved') ? now() : $complaint->resolved_at,
        ]);

        return back()->with('success', "Status komplain pesanan {$complaint->order_number} berhasil diperbarui menjadi {$complaint->status_label}.");
    }
}
