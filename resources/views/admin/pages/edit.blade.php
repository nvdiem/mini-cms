<x-admin.layout :title="'Edit Page · Mini CMS'" :crumb="'Edit Page'">
  <form method="POST" action="{{ route('admin.pages.update', $page) }}">
    @csrf
    @method('PUT')
    @include('admin.pages._form')
  </form>
</x-admin.layout>
