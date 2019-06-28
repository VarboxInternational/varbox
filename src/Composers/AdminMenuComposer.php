<?php

namespace Varbox\Composers;

use Illuminate\View\View;
use Varbox\Helpers\AdminMenuHelper;
use Varbox\Menu\MenuItem;

class AdminMenuComposer
{
    /**
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $user = auth()->user();
        $menu = menu_admin()->make(function (AdminMenuHelper $menu) {
            $menu->add(function (MenuItem $item) {
                $item->name('Home')->url(route('admin'))->data('icon', 'fa-home')->active('admin');
            });

            /*if (\Varbox::moduleEnabled('cms')) {
                $menu->add(function ($item) use ($menu) {
                    $cms = $item->name('Manage Content')->data('icon', 'fa-pencil-square-o')
                        ->permissions('pages-list', 'menus-list', 'blocks-list', 'emails-list', 'layouts-list')
                        ->active('admin/pages/*', 'admin/menus/*', 'admin/blocks/*', 'admin/emails/*', 'admin/layouts/*');

                    $menu->child($cms, function (MenuItem $item) {
                        $item->name('Pages')->url(route('admin.pages.index'))->permissions('pages-list')->active('admin/pages/*');
                    });

                    $menu->child($cms, function (MenuItem $item) {
                        $item->name('Menus')->url(route('admin.menus.locations'))->permissions('menus-list')->active('admin/menus/*');
                    });

                    $menu->child($cms, function (MenuItem $item) {
                        $item->name('Blocks')->url(route('admin.blocks.index'))->permissions('blocks-list')->active('admin/blocks/*');
                    });

                    $menu->child($cms, function (MenuItem $item) {
                        $item->name('Emails')->url(route('admin.emails.index'))->permissions('emails-list')->active('admin/emails/*');
                    });

                    $menu->child($cms, function (MenuItem $item) {
                        $item->name('Layouts')->url(route('admin.layouts.index'))->permissions('layouts-list')->active('admin/layouts/*');
                    });
                });
            }*/

            /*if (\Varbox::moduleEnabled('shop')) {
                $menu->add(function ($item) use ($menu) {
                    $shop = $item->name('Shop Panel')->data('icon', 'fa-shopping-cart')
                        ->permissions('orders-list', 'carts-list', 'product-categories-list', 'products-list', 'attributes-list', 'discounts-list', 'taxes-list', 'currencies-list')
                        ->active('admin/orders/*', 'admin/carts/*', 'admin/product-categories/*', 'admin/products/*', 'admin/sets/*', 'admin/attributes/*', 'admin/discounts/*', 'admin/taxes/*', 'admin/currencies/*');

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Orders')->url(route('admin.orders.index'))->permissions('orders-list')->active('admin/orders/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Carts')->url(route('admin.carts.index'))->permissions('carts-list')->active('admin/carts/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Categories')->url(route('admin.product_categories.index'))->permissions('product-categories-list')->active('admin/product-categories/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Products')->url(route('admin.products.index'))->permissions('products-list')->active('admin/products/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Attributes')->url(route('admin.attribute_sets.index'))->permissions('attributes-list')->active('admin/sets/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Discounts')->url(route('admin.discounts.index'))->permissions('discounts-list')->active('admin/discounts/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Taxes')->url(route('admin.taxes.index'))->permissions('taxes-list')->active('admin/taxes/*');
                    });

                    $menu->child($shop, function (MenuItem $item) {
                        $item->name('Currencies')->url(route('admin.currencies.index'))->permissions('currencies-list')->active('admin/currencies/*');
                    });
                });
            }*/

            $menu->add(function ($item) use ($menu) {
                $auth = $item->name('Access Control')->data('icon', 'fa-sign-in')
                    ->permissions('users-list', 'admins-list', 'roles-list', 'permissions-list', 'activity-list', 'notifications-list')
                    ->active('admin/users/*', 'admin/admins/*', 'admin/roles/*', 'admin/permissions/*', 'admin/activity/*', 'admin/notifications/*');

                $menu->child($auth, function (MenuItem $item) {
                    $item->name('Users')->url(route('admin.users.index'))->permissions('users-list')->active('admin/users/*');
                });

                $menu->child($auth, function (MenuItem $item) {
                    $item->name('Admins')->url(route('admin.admins.index'))->permissions('admins-list')->active('admin/admins/*');
                });

                $menu->child($auth, function (MenuItem $item) {
                    $item->name('Roles')->url(route('admin.roles.index'))->permissions('roles-list')->active('admin/roles/*');
                });

                $menu->child($auth, function (MenuItem $item) {
                    $item->name('Permissions')->url(route('admin.permissions.index'))->permissions('permissions-list')->active('admin/permissions/*');
                });

                if (\Varbox::moduleEnabled('audit')) {
                    $menu->child($auth, function (MenuItem $item) {
                        $item->name('Activity')->url(route('admin.activity.index'))->permissions('activity-list')->active('admin/activity/*');
                    });

                    $menu->child($auth, function (MenuItem $item) {
                        $item->name('Notifications')->url(route('admin.notifications.index'))->permissions('notifications-list')->active('admin/notifications/*');
                    });
                }
            });

            /*if (\Varbox::moduleEnabled('geo')) {
                $menu->add(function ($item) use ($menu) {
                    $geo = $item->name('Geo Location')->data('icon', 'fa-map-marker')
                        ->permissions('countries-list', 'states-list', 'cities-list')
                        ->active('admin/countries/*', 'admin/states/*', 'admin/cities/*');

                    $menu->child($geo, function (MenuItem $item) {
                        $item->name('Countries')->url(route('admin.countries.index'))->permissions('countries-list')->active('admin/countries/*');
                    });

                    $menu->child($geo, function (MenuItem $item) {
                        $item->name('States')->url(route('admin.states.index'))->permissions('states-list')->active('admin/states/*');
                    });

                    $menu->child($geo, function (MenuItem $item) {
                        $item->name('Cities')->url(route('admin.cities.index'))->permissions('cities-list')->active('admin/cities/*');
                    });
                });
            }*/

            /*if (\Varbox::moduleEnabled('trans')) {
                $menu->add(function ($item) use ($menu) {
                    $trans = $item->name('Multi Language')->data('icon', 'fa-globe')
                        ->permissions('translations-list', 'languages-list')
                        ->active('admin/translations/*', 'admin/languages/*');

                    $menu->child($trans, function (MenuItem $item) {
                        $item->name('Translations')->url(route('admin.translations.index'))->permissions('translations-list')->active('admin/translations/*');
                    });

                    $menu->child($trans, function (MenuItem $item) {
                        $item->name('Languages')->url(route('admin.languages.index'))->permissions('languages-list')->active('admin/languages/*');
                    });
                });
            }*/

            /*if (\Varbox::moduleEnabled('seo')) {
                $menu->add(function ($item) use ($menu) {
                    $seo = $item->name('Seo Administration')->data('icon', 'fa-bar-chart')
                        ->permissions('analytics-show', 'sitemap-list', 'redirects-list')
                        ->active('admin/analytics/*', 'admin/sitemap/*', 'admin/redirects/*');

                    $menu->child($seo, function (MenuItem $item) {
                        $item->name('Analytics')->url(route('admin.analytics.show'))->permissions('analytics-show')->active('admin/analytics/*');
                    });

                    $menu->child($seo, function (MenuItem $item) {
                        $item->name('Sitemap')->url(route('admin.sitemap.index'))->permissions('sitemap-list')->active('admin/sitemap/*');
                    });

                    $menu->child($seo, function (MenuItem $item) {
                        $item->name('Redirects')->url(route('admin.redirects.index'))->permissions('redirects-list')->active('admin/redirects/*');
                    });
                });
            }*/

            /*if (\Varbox::moduleEnabled('media')) {
                $menu->add(function ($item) use ($menu) {
                    $media = $item->name('Media Library')->data('icon', 'fa-copy')
                        ->permissions('uploads-list')
                        ->active('admin/uploads/*');

                    $menu->child($media, function (MenuItem $item) {
                        $item->name('Uploads')->url(route('admin.uploads.index'))->permissions('uploads-list')->active('admin/uploads/*');
                    });
                });
            }*/

            /*if (\Varbox::moduleEnabled('sys')) {
                $menu->add(function ($item) use ($menu) {
                    $sys = $item->name('System Settings')->data('icon', 'fa-cog')
                        ->permissions('configs-list', 'errors-list', 'backups-list')
                        ->active('admin/config/*', 'admin/errors/*', 'admin/backups/*');

                    $menu->child($sys, function (MenuItem $item) {
                        $item->name('Configs')->url(route('admin.configs.index'))->permissions('configs-list')->active('admin/configs/*');
                    });

                    $menu->child($sys, function (MenuItem $item) {
                        $item->name('Errors')->url(route('admin.errors.index'))->permissions('errors-list')->active('admin/errors/*');
                    });

                    $menu->child($sys, function (MenuItem $item) {
                        $item->name('Backups')->url(route('admin.backups.index'))->permissions('backups-list')->active('admin/backups/*');
                    });
                });
            }*/
        })->filter(function (MenuItem $item) use ($user) {
            return $user->isSuper() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}
