<?php

declare(strict_types=1);

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CrudHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {

        $this->requestStack = $requestStack;
    }

    public function response(array $data, $status = 200, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @required
     * @param Request $request
     * @return Request
     */
    public function transformJsonBody(): Request
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $data = json_decode($request->getContent(), true);
        }

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @return JsonResponse
     */
    public function userNotFound(): JsonResponse
    {
        $data = [
            'status' => 404,
            'message' => "User not found"
        ];
        return $this->response($data, 404);
    }
}