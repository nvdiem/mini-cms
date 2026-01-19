<x-admin.layout :title="'Create Page · Mini CMS'" :crumb="'New Page'">
  <form method="POST" action="{{ route('admin.pages.store') }}">
    @csrf
    @include('admin.pages._form')
  </form>
</x-admin.layout>
