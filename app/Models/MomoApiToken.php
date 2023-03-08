<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MomoApiToken extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'momoapi_token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'access_token',
        'refresh_token',
        'token_type',
        'product',
        'expires_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
        'deleted_at',
    ];

    /**
     * Set access token.
     *
     * @param string $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }
//
    /**
     * Get access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }
//
    /**
     * Set refresh token.
     *
     * @param string $refresh_token
     */
    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }
//
    /**
     * Get refresh token.
     *
     * @return string|null
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }
//
    /**
     * Set token type.
     *
     * @param string $token_type
     */
    public function setTokenType($token_type)
    {
        $this->token_type = $token_type;
    }
//
    /**
     * Get token type.
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->token_type;
    }
//
    /**
     * Set expires at.
     *
     * @param string|null $expires_at
     */
    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;
    }
//
    /**
     * Get expires at.
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * Determine if a token is expired.
     *
     * @return bool
     */
    public function isExpired()
    {

        if (is_null($this->expires_at)) {
            return false;
        }

        $expires_at = \DateTime::createFromFormat('Y-m-d H:i:s', $this->expires_at);

        $now = new \DateTime();

        return $now > $expires_at;
    }


}
