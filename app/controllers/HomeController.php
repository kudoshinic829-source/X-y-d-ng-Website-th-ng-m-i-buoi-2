<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $products = Product::getHomeProducts();
        $this->view('home/index', [
            'pageTitle' => 'Trang chủ',
            'products' => $products
        ]);
    }
}
