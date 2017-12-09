@extends('layouts.app')

@section('content')
  <div class="container">
    <h1>{{ $post->title }}</h1>
    {{ $post->updated_at->toFormattedDateString() }}
    @if ($post->published)
      <span class="label label-success" style="margin-left:15px;">Published</span>
    @else
      <span class="label label-default" style="margin-left:15px;">Draft</span>
    @endif
    <hr />
    <p class="lead">
      {{ $post->content }}
    </p>

    <div class="alert alert-info">
      @{{ count }} people are reading this right now.
      <ul style="margin-top:10px">
        <li v-for="viewer in viewers">
          Id: @{{ viewer.id }} - @{{ viewer.name }}
        </li>
      </ul>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    let app = new Vue({
      el: '#app',
      data: {
        viewers: [],
        count: 0
      },
      mounted() {
        this.listen();
      },
      methods: {
        listen() {
          Echo.join('posts.'+'{{ $post->id }}')
              .here((users) => {
                this.count = users.length;
                this.viewers = users;
              })
              .joining((user) => {
                this.count++;
                this.viewers.push(user);
              })
              .leaving((user) => {
                this.count--;
                _.pullAllBy(this.viewers, [user]);
              });
        }
      }
    });
    </script>
@endsection
