<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not change the request or response,
     * unless it needs to.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('user_id')) {
            return redirect()->to('/auth/login')->with('error', 'Please login first');
        }

        // Optional: Check user role if arguments provided
        if ($arguments) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this page');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not need to do anything
     * and can be empty.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
