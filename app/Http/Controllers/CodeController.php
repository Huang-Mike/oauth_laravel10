<?php

namespace App\Http\Controllers;

use App\Repositories\CodeRepository;

class CodeController extends Controller
{
    protected CodeRepository $codeRepo;

    public function __construct(CodeRepository $codeRepo)
    {
        $this->codeRepo = $codeRepo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param String $client_id
     * @return object
     */
    public function store(String $client_id)
    {
        return $this->codeRepo->create($client_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  String  $code_id
     * @return object
     */
    public function show(String $code_id)
    {
        return $this->codeRepo->find($code_id);

    }

    /**
     * Revoke code.
     *
     * @param String $code_id
     * @return void
     */
    public function revoke(String $code_id)
    {
        return $this->codeRepo->update($code_id, ['revoked' => 1]);
    }

    public function isAvailable(String $code_id)
    {
        return $this->codeRepo->isAvailable($code_id);
    }

    /**
     * Confirm that the Code parameter exists and hasn't expired.
     *
     * @param String $code_id
     * @return Object
     */
    public function check(String $code_id)
    {
        if (empty($code_id)) {
            $error[] = array (
                'statusCode' => 422,
                'message' => 'Missing required parameter.'
            );
        } else {
            $code = self::isAvailable($code_id);
            if (empty($code)) {
                $error[] = array (
                    'statusCode' => 408,
                    'message' => 'This page has expired.'
                );
            }
        }

        return $error ?? null;
    }
}
