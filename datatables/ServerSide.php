<?php

namespace Lz\Laravel\Datatables;

trait ServerSide
{
    public function datatables($queryBuilder, $request)
    {
        $columns = $request->input('columns');
        $orders   = $request->input('order');

        $page = ($request->start / $request->length);
        $current_page = ($page == 0) ? 1 : $page + 1;
        $request->merge(['page' => $current_page]);

        if (strlen($request->search['value']) > 0) {
            foreach ($columns as $field) {
                $queryBuilder->orWhere($field['data'], "LIKE", "%" . $request->search['value'] . "%");
            }
        }

        foreach ($orders as $order) {
            $queryBuilder->orderBy($columns[$order["column"]]["data"], $order["dir"]);
        }

        $response = $queryBuilder->paginate($request->length);

        $response = $response->toArray();

        $response['recordsTotal'] = $response['total'];
        $response['recordsFiltered'] = $response['total'];

        return $response;
    }
}
