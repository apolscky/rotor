<?php

class Bookmark extends BaseModel
{

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Возвращает топик
     */
    public function topic()
    {
        return $this->belongsTo('Topic', 'topic_id');
    }

    /**
     * Возвращает модель топика
     */
    public function getTopic()
    {
        return $this->topic ? $this->topic : new Topic();
    }
}
