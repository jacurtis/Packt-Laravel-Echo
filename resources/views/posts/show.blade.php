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
    <div style="margin-bottom:20px;">
      <textarea id="commentBody" class="form-control" rows="3" name="body" placeholder="Contribute your two cents." v-model="commentBox" @enter.prevent="postComment"></textarea>
      <button class="btn btn-success" style="margin-top:10px" @click.prevent="postComment">Save Comment</button>
    </div>


    <div class="media" style="margin-top:20px;" v-for="comment in comments">
      <div class="media-left">
        <a href="#">
          <img class="media-object" src="http://placeimg.com/80/80" alt="...">
        </a>
      </div>
      <div class="media-body">
        <h4 class="media-heading">@{{ comment.user.name }} said...</h4>
        <p>
          @{{ comment.body }}
        </p>
        <span style="color: #aaa;">on @{{ comment.created_at }}</span>
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
        comments: {},
        commentBox: ''
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
          .then((response) => {
            this.comments = response.data
          })
          .catch(function (error) {
            console.log(error);
          })
        },
        postComment() {
          axios.post('/api/posts/'+this.post.id+'/comment', {
            api_token: this.user.api_token,
            body: this.commentBox
          })
          .then((response) => {
            this.commentBox = '';
            this.comments.unshift(response.data)
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
              })
              .listen('NewComment', (comment) => {
                this.comments.unshift(comment);
              });
        }
      }
    });
    </script>
@endsection
