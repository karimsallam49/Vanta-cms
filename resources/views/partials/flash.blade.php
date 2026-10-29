@if(session('flash'))
  <div class="flash {{ session('flash')['type'] ?? 'success' }}" role="status">
    {{ session('flash')['message'] ?? '' }}
  </div>
@endif
