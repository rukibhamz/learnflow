<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BlogPost extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /**
     * Get the table of contents from the content.
     */
    public function getTocAttribute()
    {
        preg_match_all('/<(h1|h2|h3)[^>]*>(.*?)<\/\1>/i', $this->content, $matches, PREG_SET_ORDER);
        
        $toc = [];
        foreach ($matches as $match) {
            $level = strtolower($match[1]);
            $text = strip_tags($match[2]);
            $id = \Illuminate\Support\Str::slug($text);
            
            $toc[] = [
                'level' => $level,
                'text' => $text,
                'id' => $id,
            ];
        }
        
        return $toc;
    }

    /**
     * Get the content with IDs injected into the headings.
     */
    public function getContentWithIdsAttribute()
    {
        return preg_replace_callback('/<(h1|h2|h3)([^>]*)>(.*?)<\/\1>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];
            $text = $matches[3];
            $id = \Illuminate\Support\Str::slug(strip_tags($text));
            
            // Check if ID already exists in attributes
            if (strpos($attributes, 'id=') === false) {
                return "<{$tag}{$attributes} id=\"{$id}\">{$text}</{$tag}>";
            }
            
            return $matches[0];
        }, $this->content);
    }
}
