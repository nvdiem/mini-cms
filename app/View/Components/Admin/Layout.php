<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Layout extends Component
{
    public string $title;
    public string $crumb;

    public function __construct($title = 'Mini CMS', $crumb = 'Admin')
    {
        $this->title = $title;
        $this->crumb = $crumb;
    }

    public function render()
    {
        return view('admin.layout');
    }
}
