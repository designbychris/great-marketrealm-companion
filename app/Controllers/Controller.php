<?php 
class DashboardController extends Controller
{
    public function index()
    {
        return $this->view(
            'dashboard.index',
            [
                'characters' => [],
            ]
        );
    }
}
