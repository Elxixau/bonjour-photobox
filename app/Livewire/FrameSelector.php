<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Frame;

class FrameSelector extends Component
{
    public $frames;
    public $selectedFrame = null;

    public $orderCode;
    public $layout;

    public function mount($orderCode, $layout, $selectedFrame = null)
    {
        $this->frames = Frame::all();
        $this->orderCode = $orderCode;
        $this->layout = $layout;
        $this->selectedFrame = $selectedFrame;
    }

    public function selectFrame($frameId)
    {
        $this->selectedFrame = $frameId;
    }

    public function render()
    {
        return view('livewire.frame-selector');
    }
}
