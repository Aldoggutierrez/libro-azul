<?php

namespace Abiside\LibroAzul\Services;

use Artisaninweb\SoapWrapper\SoapWrapper;
use PhpParser\Node\Expr\Cast\Object_;
use SoapClient;
use SoapFault;

class LibroAzul
{
    /**
     * Webservice base URL to consume the "libro azul" resources
     *
     * @var string
     * */
    const SERVICE_URL = 'https://www.libroazul.com/ws/wslibroazul.asmx?WSDL';

    /**
     * SOAP object to make the requests
     *
     * @var \Artisaninweb\SoapWrapper\SoapWrapper
     */
    protected $webservice;

    /**
     * Libro Azul Session Token
     *
     * @var string
     */
    protected $sessionToken = null;

    public function __construct()
    {
        // Generate the soap object to make the requests
        $this->webservice = new SoapClient(self::SERVICE_URL, [
            'uri'=>'http://testuri.org',
            'trace' => true,
            'exception' => true,
            'connection_timeout'=>9999
        ]);

        if (is_null($this->sessionToken))
            $this->sessionToken = $this->getSessionToken();
    }

    /**
     * Get the session token to make requests
     *
     * @return string
     */
    public function getSessionToken()
    {
        $response = $this->call('IniciarSesion', [
            'Usuario' => config('services.libroazul.user'),
            'Contrasena' => config('services.libroazul.password'),
        ]);

        return $response->IniciarSesionResult;
    }

    /**
     * Get the list of avialable years
     *
     * @return string
     */
    public function getYears()
    {
        $response = $this->call('ObtenerAnios', [
            'Llave' => $this->sessionToken,
            'Clase' => 0,
            'Edicion' => 0,
        ]);

        return $this->mapCatalog($response->ObtenerAniosResult->Catalogo);
    }


    /**
     * Get the list of avialable years
     *
     * @param  int  $year
     * @return string
     */
    public function getBrandsByYear(int $year)
    {
        $response = $this->call('ObtenerMarcasPorAnio', [
            'Llave' => $this->sessionToken,
            'Clase' => 0,
            'ClaveAnio' => $year,
            'Edicion' => 0,
        ]);

        return $this->mapCatalog($response->ObtenerMarcasPorAnioResult->Catalogo);
    }

    /**
     * Get the list of models by year and brand
     *
     * @param  int  $year
     * @param  int  $brand
     * @return string
     */
    public function getModelsByYearAndBrand(int $year, int $brand)
    {
        $response = $this->call('ObtenerModelosPorAnioMarca', [
            'Llave' => $this->sessionToken,
            'Clase' => 0,
            'ClaveAnio' => $year,
            'ClaveMarca' => $brand,
            'Edicion' => 0,
        ]);

        return $this->mapCatalog($response->ObtenerModelosPorAnioMarcaResult->Catalogo);
    }

    /**
     * Get the list of versions by model, year and brand
     *
     * @param  int  $year
     * @param  int  $brand
     * @param  int  $model
     * @return string
     */
    public function getVersionsByYearBrandAndModel(int $year, int $brand, int $model)
    {
        $response = $this->call('ObtenerVersionesPorAnioMarcaModelo', [
            'Llave' => $this->sessionToken,
            'Clase' => 0,
            'ClaveAnio' => $year,
            'ClaveMarca' => $brand,
            'ClaveModelo' => $model,
            'Edicion' => 0,
        ]);

        return $this->mapCatalog($response->ObtenerVersionesPorAnioMarcaModeloResult->Catalogo);
    }


    /**
     * Get the price for the given version
     *
     * @param  int  $price
     * @return string
     */
    public function getPrice(int $version)
    {
        $response = $this->call('ObtenerPrecioVersionPorClave', [
            'Llave' => $this->sessionToken,
            'ClaveVersion' => $version,
            'Clase' => 0,
            'Edicion' => 0,
        ]);

        $result = $response->ObtenerPrecioVersionPorClaveResult;

        return (Object) [
            'sell' => $result->Venta,
            'buy' => $result->Compra,
            'currency' => $result->Moneda,
        ];
    }

    /**
     * Make a call of a SOAP Action and catch errors
     *
     * @param  string  $method
     * @param  array  $params
     * @return array|string|object
     */
    protected function call(string $method, array $params)
    {
        try {
            return $this->webservice->{$method}($params);
        } catch (SoapFault $e) {
            return $e;
        }
    }

    public function mapCatalog($result)
    {
        $items = collect($result);

        return $items->map(function($item) {
            return (Object) [
                'id' => $item->Clave,
                'name' => $item->Nombre
            ];
        });
    }

}