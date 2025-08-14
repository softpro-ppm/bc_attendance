<?php

namespace App\Core;

class Pagination
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->config = require_once __DIR__ . '/../../config/config.php';
    }

    public function paginate($sql, $params = [], $options = [])
    {
        $defaultOptions = [
            'page' => 1,
            'perPage' => $this->config['pagination']['default_per_page'],
            'sortBy' => null,
            'sortDir' => 'ASC',
            'search' => null,
            'searchColumns' => [],
            'whereConditions' => []
        ];

        $options = array_merge($defaultOptions, $options);
        
        // Build the base query
        $baseSql = $this->buildBaseQuery($sql, $options);
        
        // Get total count
        $countSql = $this->buildCountQuery($baseSql);
        $total = $this->db->fetchColumn($countSql, $params);
        
        // Apply pagination
        $perPage = $options['perPage'] === 'all' ? $total : (int) $options['perPage'];
        $page = max(1, (int) $options['page']);
        $offset = ($page - 1) * $perPage;
        
        // Build final query with sorting and pagination
        $finalSql = $this->buildFinalQuery($baseSql, $options, $perPage, $offset);
        
        // Execute query
        $rows = $this->db->fetchAll($finalSql, $params);
        
        // Calculate pagination info
        $totalPages = $perPage === 'all' ? 1 : ceil($total / $perPage);
        $from = $total > 0 ? ($page - 1) * $perPage + 1 : 0;
        $to = min($page * $perPage, $total);
        
        return [
            'data' => $rows,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'from' => $from,
                'to' => $to,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_page' => $page > 1 ? $page - 1 : null,
                'next_page' => $page < $totalPages ? $page + 1 : null,
                'first_page' => 1,
                'last_page' => $totalPages
            ],
            'sorting' => [
                'sort_by' => $options['sortBy'],
                'sort_dir' => $options['sortDir']
            ],
            'search' => [
                'query' => $options['search'],
                'columns' => $options['searchColumns']
            ]
        ];
    }

    private function buildBaseQuery($sql, $options)
    {
        $query = $sql;
        
        // Add WHERE conditions
        if (!empty($options['whereConditions'])) {
            $whereClause = ' WHERE ' . implode(' AND ', $options['whereConditions']);
            if (stripos($query, 'WHERE') !== false) {
                $query = str_replace('WHERE', $whereClause . ' AND', $query);
            } else {
                $query .= $whereClause;
            }
        }
        
        // Add search
        if (!empty($options['search']) && !empty($options['searchColumns'])) {
            $searchConditions = [];
            foreach ($options['searchColumns'] as $column) {
                $searchConditions[] = "{$column} LIKE ?";
            }
            
            $searchClause = ' AND (' . implode(' OR ', $searchConditions) . ')';
            
            if (stripos($query, 'WHERE') !== false) {
                $query .= $searchClause;
            } else {
                $query .= ' WHERE (' . implode(' OR ', $searchConditions) . ')';
            }
        }
        
        return $query;
    }

    private function buildCountQuery($baseSql)
    {
        // Convert SELECT query to COUNT query
        $countSql = preg_replace('/SELECT\s+.+?\s+FROM/i', 'SELECT COUNT(*) FROM', $baseSql);
        
        // Remove ORDER BY clause if present
        $countSql = preg_replace('/ORDER\s+BY\s+.+$/i', '', $countSql);
        
        return $countSql;
    }

    private function buildFinalQuery($baseSql, $options, $perPage, $offset)
    {
        $query = $baseSql;
        
        // Add sorting
        if ($options['sortBy']) {
            $query .= " ORDER BY {$options['sortBy']} {$options['sortDir']}";
        }
        
        // Add pagination (unless 'all' is selected)
        if ($perPage !== 'all') {
            $query .= " LIMIT {$perPage} OFFSET {$offset}";
        }
        
        return $query;
    }

    public function getPageSizes()
    {
        return $this->config['pagination']['page_sizes'];
    }

    public function buildSearchParams($search, $searchColumns)
    {
        if (empty($search) || empty($searchColumns)) {
            return [];
        }
        
        $params = [];
        foreach ($searchColumns as $column) {
            $params[] = "%{$search}%";
        }
        
        return $params;
    }

    public function renderPaginationLinks($pagination, $baseUrl, $queryParams = [])
    {
        if ($pagination['total_pages'] <= 1) {
            return '';
        }
        
        $html = '<nav class="pagination-nav" aria-label="Pagination">';
        $html .= '<ul class="pagination">';
        
        // Previous button
        if ($pagination['has_previous']) {
            $prevUrl = $this->buildUrl($baseUrl, array_merge($queryParams, ['page' => $pagination['previous_page']]));
            $html .= "<li class='page-item'><a class='page-link' href='{$prevUrl}'>&laquo; Previous</a></li>";
        } else {
            $html .= "<li class='page-item disabled'><span class='page-link'>&laquo; Previous</span></li>";
        }
        
        // Page numbers
        $startPage = max(1, $pagination['current_page'] - 2);
        $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
        
        if ($startPage > 1) {
            $firstUrl = $this->buildUrl($baseUrl, array_merge($queryParams, ['page' => 1]));
            $html .= "<li class='page-item'><a class='page-link' href='{$firstUrl}'>1</a></li>";
            if ($startPage > 2) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $pagination['current_page']) {
                $html .= "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
            } else {
                $pageUrl = $this->buildUrl($baseUrl, array_merge($queryParams, ['page' => $i]));
                $html .= "<li class='page-item'><a class='page-link' href='{$pageUrl}'>{$i}</a></li>";
            }
        }
        
        if ($endPage < $pagination['total_pages']) {
            if ($endPage < $pagination['total_pages'] - 1) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
            $lastUrl = $this->buildUrl($baseUrl, array_merge($queryParams, ['page' => $pagination['total_pages']]));
            $html .= "<li class='page-item'><a class='page-link' href='{$lastUrl}'>{$pagination['total_pages']}</a></li>";
        }
        
        // Next button
        if ($pagination['has_next']) {
            $nextUrl = $this->buildUrl($baseUrl, array_merge($queryParams, ['page' => $pagination['next_page']]));
            $html .= "<li class='page-item'><a class='page-link' href='{$nextUrl}'>Next &raquo;</a></li>";
        } else {
            $html .= "<li class='page-item disabled'><span class='page-link'>Next &raquo;</span></li>";
        }
        
        $html .= '</ul>';
        $html .= '</nav>';
        
        return $html;
    }

    private function buildUrl($baseUrl, $params)
    {
        if (empty($params)) {
            return $baseUrl;
        }
        
        $queryString = http_build_query($params);
        return $baseUrl . '?' . $queryString;
    }
}
