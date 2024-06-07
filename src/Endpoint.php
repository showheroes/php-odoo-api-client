<?php

namespace Ang3\Component\Odoo;

use Ang3\Component\Odoo\Exception\RemoteException;
use Ang3\Component\Odoo\Exception\RequestException;
use Ang3\Component\XmlRpc\Client as XmlRpcClient;
use Ang3\Component\XmlRpc\Exception\RemoteException as XmlRpcRemoteException;
use Ang3\Component\XmlRpc\Transport\TransportInterface;
use Throwable;

class Endpoint
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var XmlRpcClient
     */
    private $client;

    public function __construct(string $url, TransportInterface $transport = null, array $rpcClientOptions = [])
    {
        $this->url = $url;
        $this->client = new XmlRpcClient($url, $transport, $rpcClientOptions);
    }

    /**
     * @throws RequestException when request failed
     *
     * @return mixed
     */
    public function call(string $method, array $args = [])
    {
        try {
            return $this->client->call($method, $args);
        } catch (XmlRpcRemoteException $exception) {
            if (preg_match('#cannot marshal None unless allow_none is enabled#', $exception->getMessage())) {
                return null;
            }

            throw RemoteException::create($exception);
        } catch (Throwable $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getClient(): XmlRpcClient
    {
        return $this->client;
    }
}
