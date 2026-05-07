<?php

namespace App\Service;

use App\Entity\Comment;

class CommentSentimentService
{
    /**
     * @return array{label:string,emoji:string,class:string}
     */
    public function detect(Comment $comment): array
    {
        $likes = $comment->getLikesCount();
        $dislikes = $comment->getDislikesCount();

        if ($likes > $dislikes) {
            return [
                'label' => 'Positive',
                'emoji' => '😊',
                'class' => 'sentiment-positive',
            ];
        }

        if ($likes < $dislikes) {
            return [
                'label' => 'Negative',
                'emoji' => '😡',
                'class' => 'sentiment-negative',
            ];
        }

        return [
            'label' => 'Neutral',
            'emoji' => '😐',
            'class' => 'sentiment-neutral',
        ];
    }
}
