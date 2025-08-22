<?php

namespace App\Filament\Resources\FrameResource\Pages;

use App\Filament\Resources\FrameResource;
use App\Models\Frame;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class FrameLayoutEditor extends Page
{
    protected static string $resource = FrameResource::class;
    protected static string $view = 'filament.resources.frame-resource.pages.frame-layout-editor';

    public Frame $frame;

     /** supaya bisa diisi dari frontend (Alpine/Livewire) */
    public array $slots = [];

    public function mount($record): void
    {
        $this->frame = Frame::with('layouts')->findOrFail($record);
        
        $this->slots = $this->frame->layouts->map(fn($l) => [
            'id' => (int) $l->id,
            'slot_number' => (int) $l->slot_number,
            'x' => (int) $l->x,
            'y' => (int) $l->y,
            'width' => (int) $l->width,
            'height' => (int) $l->height,
        ])->values()->toArray();
    }

    protected function getViewData(): array
    {
        return [
            'frame' => $this->frame,
        ];
    }

    /** Simpan perubahan 1 slot */
    /** Simpan perubahan 1 slot (auto-save) */
public function updateLayout(int $id, array $data): void
{
    $layout = $this->frame->layouts()->findOrFail($id);
    $layout->update([
        'x' => (int) $data['x'],
        'y' => (int) $data['y'],
        'width'  => (int) $data['width'],
        'height' => (int) $data['height'],
    ]);

    Notification::make()
        ->title("Slot #{$layout->slot_number} berhasil disimpan otomatis!")
        ->success()
        ->send();
}


    /** Tambah slot baru cepat */
    public function createSlot(): array
{
    $slot = $this->frame->layouts()->create([
        'slot_number' => ($this->frame->layouts()->max('slot_number') ?? 0) + 1,
        'x' => 20, 'y' => 20, 'width' => 150, 'height' => 200,
    ]);

    return [
        'id' => (int) $slot->id,
        'slot_number' => (int) $slot->slot_number,
        'x' => (int) $slot->x,
        'y' => (int) $slot->y,
        'width' => (int) $slot->width,
        'height' => (int) $slot->height,
    ];
}

    /** Hapus slot */
    public function deleteSlot(int $id): void
    {
        $this->frame->layouts()->whereKey($id)->delete();
        $this->frame->refresh();
    }

    
  
    /** Simpan semua slot */
    public function saveAll(): void
    {
        foreach ($this->slots as $slot) {
            $this->frame->layouts()->updateOrCreate(
                ['id' => $slot['id'] ?? null],
                [
                    'slot_number' => $slot['slot_number'],
                    'x' => (int) $slot['x'],
                    'y' => (int) $slot['y'],
                    'width' => (int) $slot['width'],
                    'height' => (int) $slot['height'],
                ]
            );
        }

        $this->frame->refresh();

          Notification::make()
        ->title('Layout berhasil disimpan!')
        ->success()
        ->send();
    }

}
