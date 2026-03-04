<?php

namespace App\Enums;

enum RowCategory: string
{
    case Blank = 'blank';
    case NotFound = '404';
    case Login = 'login';
    case Blog = 'blog';
    case BlogDetail = 'blog-detail';
    case Contact = 'contact';
    case Hero = 'hero';
    case Content = 'content';
    case Cta = 'cta';
    case ECommerce = 'e-commerce';
    case Faqs = 'faqs';
    case Features = 'features';
    case Footer = 'footer';
    case Header = 'header';
    case Gallery = 'gallery';
    case IconList = 'icon-list';
    case Pricing = 'pricing';
    case Slider = 'slider';
    case SocialProof = 'social-proof';

    public function label(): string
    {
        return ucwords(str_replace('-', ' ', $this->value));
    }
}
