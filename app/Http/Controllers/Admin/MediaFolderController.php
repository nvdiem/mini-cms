<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFolder;
use Illuminate\Http\Request;

class MediaFolderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        MediaFolder::create($data);

        return back()->with('toast', ['tone'=>'success','title'=>'Success','message'=>'Folder created.']);
    }

    public function update(Request $request, MediaFolder $folder)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $folder->update($data);

        return back()->with('toast', ['tone'=>'success','title'=>'Success','message'=>'Folder renamed.']);
    }

    public function destroy(MediaFolder $folder)
    {
        // Media will set folder_id to null on delete due to DB constraint (nullOnDelete)
        // or we can explicitly handle it if we want to delete contents.
        // User requirements say "delete (confirm)". Assuming folder delete, media becomes Unsorted (safe safe).
        $folder->delete();

        return redirect()->route('admin.media.index')->with('toast', ['tone'=>'success','title'=>'Deleted','message'=>'Folder deleted.']);
    }
}
