<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredOrders extends Command
{
     protected $signature = 'orders:delete-expired';
    protected $description = 'Delete expired orders and related photos';

    public function handle()
    {
        // Misalnya kita anggap order berlaku sesuai kolom waktu (hari)
        $now = Carbon::now();

        $orders = Order::with('cloudGallery')->get();

        foreach ($orders as $order) {
            if ($order->qrAccess && $order->qrAccess->expired_at < $now) {
                // Hapus foto
                foreach ($order->cloudGallery as $photo) {
                    if (\Storage::disk('public')->exists($photo->img_path)) {
                        \Storage::disk('public')->delete($photo->img_path);
                    }
                    $photo->delete();
                }

                // Hapus order
                $order->delete();

                $this->info("Order {$order->id} & related photos deleted.");
            }
        }

        return Command::SUCCESS;
    }
}
