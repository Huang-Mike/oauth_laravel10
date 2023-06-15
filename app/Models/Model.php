<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    protected $table;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'id';

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'int';

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;

    /**
     * @see HasAttributes::$casts
     */
    protected $casts = [];

    /**
     * @see HasAttributes::$dates
     */
    protected $dates = [];

    /**
     * @see HasAttributes::$dateFormat
     */
    protected $dateFormat = 'U';

    /**
     * @see HasTimestamps::$timestamps
     */
    public $timestamps = true;

    /**
     * @see GuardsAttributes::$fillable
     */
    protected $fillable = [];
}
