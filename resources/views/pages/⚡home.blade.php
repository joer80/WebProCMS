<?php

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'WebProCMS is a clean, powerful content management platform built for web professionals, developers, and agencies who demand more from their tools.'])] #[Title('WebProCMS — Build, Manage, and Publish Without Limits')] class extends Component {
    public string $websiteType = '';

    /** @var \Illuminate\Database\Eloquent\Collection<int, Post> */
    public $recentPosts;

    public function mount(): void
    {
        $this->websiteType = config('features.website_type');
        $this->recentPosts = Post::query()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->limit(3)
            ->get();
    }
}; ?>
<div>
</div>
