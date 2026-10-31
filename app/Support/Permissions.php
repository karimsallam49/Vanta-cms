<?php

namespace App\Support;

/**
 * Registry of every permission the CMS understands.
 *
 * A permission string is "{section}.{action}" — e.g. "motorcycles.edit".
 * Roles store a flat array of these strings; `is_admin` roles bypass
 * the check entirely.
 */
class Permissions
{
    /**
     * Sections in sidebar order. Each maps to action key => label so the
     * role form can render a clean checkbox matrix.
     */
    public static function sections(): array
    {
        return [
            'brands' => [
                'label' => 'Brands',
                'hint' => 'Catalogue — brand profiles',
                'actions' => self::CRUD + ['import' => 'Bulk import', 'export' => 'Export Excel'],
            ],
            'motorcycles' => [
                'label' => 'Motorcycles',
                'hint' => 'Models, pricing, stock, gallery',
                'actions' => self::CRUD + ['import' => 'Bulk import', 'export' => 'Export Excel'],
            ],
            'offers' => [
                'label' => 'Offers',
                'hint' => 'Promotions & campaigns',
                'actions' => self::CRUD,
            ],
            'test-rides' => [
                'label' => 'Test rides',
                'hint' => 'Customer booking requests',
                'actions' => ['view' => 'View requests', 'edit' => 'Update status & notes'],
            ],
            'showrooms' => [
                'label' => 'Showrooms',
                'hint' => 'Locations, hours, map pins',
                'actions' => self::CRUD,
            ],
            'site-content' => [
                'label' => 'Site content',
                'hint' => 'Website sections & copy',
                'actions' => ['view' => 'View sections', 'edit' => 'Edit content'],
            ],
            'currencies' => [
                'label' => 'Currencies',
                'hint' => 'Price currencies',
                'actions' => self::CRUD,
            ],
            'staff' => [
                'label' => 'Staff',
                'hint' => 'CMS user accounts',
                'actions' => self::CRUD,
            ],
            'roles' => [
                'label' => 'Roles',
                'hint' => 'Permission sets',
                'actions' => self::CRUD,
            ],
            'reports' => [
                'label' => 'Reports',
                'hint' => 'System-wide analytics',
                'actions' => ['view' => 'View reports', 'export' => 'Export CSV'],
            ],
            'activity' => [
                'label' => 'Activity log',
                'hint' => 'Audit trail',
                'actions' => ['view' => 'View log'],
            ],
        ];
    }

    private const CRUD = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ];

    /** Flat list of every valid "section.action" string. */
    public static function all(): array
    {
        $out = [];
        foreach (self::sections() as $section => $def) {
            foreach ($def['actions'] as $action => $_) {
                $out[] = "{$section}.{$action}";
            }
        }

        return $out;
    }

    /** Permissions granted to the seeded "Data Entry" role — same reach it had before RBAC. */
    public static function dataEntryDefaults(): array
    {
        return collect(self::all())
            ->filter(fn ($p) => str_starts_with($p, 'motorcycles.')
                || str_starts_with($p, 'brands.')
                || str_starts_with($p, 'showrooms.')
                || str_starts_with($p, 'offers.')
                || in_array($p, ['test-rides.view', 'test-rides.edit'], true))
            ->values()->all();
    }
}
