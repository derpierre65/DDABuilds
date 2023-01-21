<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends Controller
{
	public function index()
	{
		return view('index');
	}

	public function notFound()
	{
		throw new NotFoundHttpException();
	}
}