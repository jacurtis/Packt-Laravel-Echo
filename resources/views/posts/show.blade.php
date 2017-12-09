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

    <h3>Comments:</h3>
    <form action="{{ 'api/posts/'.$post->id.'/comment' }}" method="POST" style="margin-bottom:20px;">
      <textarea id="commentBody" class="form-control" rows="3" name="body" placeholder="Contribute your two cents."></textarea>
    </form>
    <div class="media" style="margin-top:20px;">
      <div class="media-left">
        <a href="#">
          <img class="media-object" src="http://placeimg.com/80/80" alt="...">
        </a>
      </div>
      <div class="media-body">
        <h4 class="media-heading">Joey said...</h4>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <span style="color: #aaa;">on March 5th, 2014</span>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    let app = new Vue({
      el: '#app',
      data: {
        viewers: [],
        count: 0,
        post: {!! json_encode($post) !!},
        user: {!! json_encode(Auth::user()) !!},
        comments: {}
      },
      mounted() {
        this.listen();
        this.getComments();
      },
      methods: {
        getComments() {
          axios.get('/api/posts/'+this.post.id+'/comments', {
            params: {
              api_token: this.user.api_token
            }
          })
          .then(function (response) {
            this.comments = response.data;
          })
          .catch(function (error) {
            console.log(error);
          })
        },
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
