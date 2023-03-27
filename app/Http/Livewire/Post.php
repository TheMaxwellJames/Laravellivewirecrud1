<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Post as Posts;

class Post extends Component
{

    public $posts, $title, $description, $postId, $updatePost = false, $addPost = false;

    protected $listeners = [
        'deletePostListener' => 'deletePost'
    ];

    protected $rules = [
        'title' => 'required',
        'description' => 'required',
    ];

    protected function resetFields() {
        $this->title = '';
        $this->description = '';
       }



    public function render()
    {   
        $this->posts = Posts::select('id', 'title', 'description')->get();
        return view('livewire.posts');
    }

    public function addPost()
    {
        $this->resetFields();
        $this->addPost = true;
        $this->updatePost = false;
    }

    public function storePost()
    {
        $this->validate();
        try {
                Posts::create([
                    'title' => $this->title,
                    'description' => $this->description
                ]);
                session()->flash('success', 'Post Created successfully!!');
                $this->resetFields();
                $this->addPost = false;

        }catch(\Exception $ex) {
            session()->flash('error', 'Something went wrong');
        }
    }


    public function editPost($id)
    {
        try {
            $post = Posts::findorFail($id);

              if(!$post) {
                session()->flash('error', 'Post not found');

              }else {
                $this->title = $post->title;
                $this->description = $post->description;
                $this->postId = $post->id;
                $this->updatePost = true;
                $this->addPost = false;
              }
       
         

    }catch(\Exception $ex) {
        session()->flash('error', 'Something went wrong');
    }

    }

    public function updatePost()
    {
        $this->validate();
        try {
            $post = Posts::find($this->postId);
            $post->title = $this->title;
            $post->description = $this->description;
            $post->save();
            session()->flash('success', 'Post Updated Successfully');
            $this->resetFields();
            $this->updatePost = false;

        }
        catch(\Exception $ex) {
            session()->flash('error', 'Something went wrong. Please try again');
        }
    }

    public function cancelPost()
    {
        $this->addPost = false;
        $this->updatePost = false;
        $this->resetFields();
    }


    public function deletePost($postId)
    {
        try{
                Posts::find($postId)->delete();
                session()->flash('success', 'Post Deleted Successfully');
        }

        catch(\Exception $ex)
        {
            session()->flash('error', 'Something went wrong');
        }
    }

}
