<?php
/**
 * Token repository.
 */

namespace App\Repositories;
use App\Models\MomoApiToken;
use App\Repositories\Interfaces\MomoApiTokenRepositoryInterface;
use Carbon\Carbon;

/**
 * Token repository.
 */
class MomoApiTokenRepository implements MomoApiTokenRepositoryInterface
{
    /**
     * Product.
     *
     * @var string
     */
    protected $product;

    /**
     * Constructor.
     *
     * @param string $product
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $token_attributes)
    {
        $token_attributes['token_type'] = 'Bearer';
        $token_attributes['product'] = $this->product;

        if (isset($token_attributes['expires_in'])) {
            $token_attributes['expires_at'] = Carbon::now()->addSeconds($token_attributes['expires_in']);
        }

        return MomoApiToken::create($token_attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveAll()
    {
        return MomoApiToken::where('product', $this->product)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve($access_token = null)
    {
        if ($access_token) {
            return MomoApiToken::query()
                ->where('access_token', $access_token)
                ->where('product', $this->product)
                ->first();
        }

        return MomoApiToken::query()
            ->where('product', $this->product)
            ->latest('created_at')
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($access_token, array $attributes)
    {
        $token = MomoApiToken::query()
            ->where('access_token', $access_token)
            ->where('product', $this->product)
            ->first();

        $token->update($attributes);

        return $token->fresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($access_token)
    {
        MomoApiToken::query()
            ->where('access_token', $access_token)
            ->where('product', $this->product)
            ->delete();
    }
}
