<?php


namespace DocSDK\Handler;


use DocSDK\Exceptions\SignatureVerificationException;
use DocSDK\Exceptions\UnexpectedDataException;
use DocSDK\Hydrator\HydratorInterface;
use DocSDK\Models\WebhookEvent;
use Psr\Http\Message\RequestInterface;

class WebhookHandler
{

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * WebhookHandler constructor.
     *
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }


    /**
     * @param string $payload
     * @param string $signature
     * @param string $signingSecret
     *
     * @return WebhookEvent
     * @throws SignatureVerificationException
     * @throws UnexpectedDataException
     */
    public function constructEvent(string $payload, string $signature, string $signingSecret): WebhookEvent
    {

        if(!hash_equals(hash_hmac('sha256', $payload, $signingSecret), $signature)) {
            throw new SignatureVerificationException("Invalid webhook signature");
        }

        return $this->hydrator->hydrateObject(new WebhookEvent(), json_decode($payload));

    }


    /**
     * @param RequestInterface $request
     * @param string           $signingSecret
     *
     * @return WebhookEvent
     * @throws SignatureVerificationException
     * @throws UnexpectedDataException
     */
    public function constructEventFromRequest(RequestInterface $request, string $signingSecret): WebhookEvent
    {
        return $this->constructEvent($request->getBody(), $request->getHeaderLine('DocSDK-Signature'),
            $signingSecret);
    }

}
