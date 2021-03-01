<?php

namespace App\Model;

use App\Entity\Category;

class SearchData
{

    /**
     * @var integer
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Category[]
     */
    public $categories = [];

    /**
     * @var null|integer
     */
    public $min;

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var boolean
     */
    public $promo;
}
