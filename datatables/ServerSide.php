<?php

namespace Lz\Laravel\Datatables;

trait ServerSide
{
    public function datatables($queryBuilder, $request)
    {
        $isDatatables = (isset($request->start) && isset($request->length));

        if ($isDatatables) {
            $columns = $request->input('columns');
            $orders   = $request->input('order');

            $page = (($request->start && $request->length)) ? ($request->start / $request->length) : 1;
            $current_page = ($page == 0) ? 1 : $page + 1;
            $request->merge(['page' => $current_page]);
            $perPage = ($request->length) ? $request->length : 10;

            if ($request->search) {
                if (strlen($request->search['value']) > 0) {
                    foreach ($columns as $field) {
                        $queryBuilder->orWhere($field['data'], "LIKE", "%{$request->search['value']}%");
                    }
                }
            }

            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $queryBuilder->orderBy($columns[$order["column"]]["data"], $order["dir"]);
                }
            }

            $response = $queryBuilder->paginate($perPage);
            $response = $response->toArray();

            $response['recordsTotal'] = $response['total'];
            $response['recordsFiltered'] = $response['total'];
        } else {
            foreach ($request->all() as $key => $value) {
                $queryBuilder->where($key, "=", $value);
            }

            $response = $queryBuilder->paginate(($request->count) ? $request->count : 10);
        }

        return $response;
    }
}
