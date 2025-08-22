<?php

namespace App\Filament\Resources\FrameResource\Pages;

use App\Filament\Resources\FrameResource;
use App\Models\Frame;
use Filament\Resources\Pages\Page;

class FrameLayoutEditor extends Page
{
    protected static string $resource = FrameResource::class;
    protected static string $view = 'filament.resources.frame-resource.pages.frame-layout-editor';

    public Frame $frame;

    public function mount($record): void
    {
        $this->frame = Frame::with('layouts')->findOrFail($record);
    }

    protected function getViewData(): array
    {
        return [
            'frame' => $this->frame,
        ];
    }

    /** Simpan perubahan 1 slot */
    public function updateLayout(int $id, array $data): void
    {
        $layout = $this->frame->layouts()->findOrFail($id);
        $layout->update([
            'x' => (int) round($data['x']),
            'y' => (int) round($data['y']),
            'width'  => (int) round($data['width']),
            'height' => (int) round($data['height']),
        ]);
        $this->frame->refresh();
    }

    /** Tambah slot baru cepat */
    public function createSlot(): int
    {
        /** @var FrameLayout $slot */
        $slot = $this->frame->layouts()->create([
            'slot_number' => ($this->frame->layouts()->max('slot_number') ?? 0) + 1,
            'x' => 20, 'y' => 20, 'width' => 150, 'height' => 200,
        ]);

        $this->frame->refresh();
        return (int) $slot->id;
    }

    /** Hapus slot */
    public function deleteSlot(int $id): void
    {
        $this->frame->layouts()->whereKey($id)->delete();
        $this->frame->refresh();
    }
}
