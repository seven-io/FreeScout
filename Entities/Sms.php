<?php

namespace Modules\Seven\Entities;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model {
    /**
     * The attributes that should be cast to native types.
     * @var array $casts
     */
    protected $casts = [
        'to' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array $fillable
     */
    protected $fillable = [
        'response',
        'text',
        'to',
    ];

    /**
     * Attributes which are not fillable using fill() method.
     * @var array $guarded
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     * @var string $table
     */
    protected $table = 'seven_sms';

    /**
     * Returns the response with duplicated values removed.
     * @return string
     */
    public function getCleanResponse(): string {
        $response = json_decode($this->response);
        unset($response->sms_type);

        foreach ($response->messages as $message) {
            unset(
                $message->encoding,
                $message->label,
                $message->sender,
                $message->text
            );
        }

        return json_encode($response);
    }

    /**
     * @param array $to
     * @return void
     */
    public function setToAttribute(array $to) {
        $this->attributes['to'] = json_encode($to);
    }
}
