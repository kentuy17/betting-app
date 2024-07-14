import * as React from 'react';
import {
  flexRender,
  getCoreRowModel,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from '@tanstack/react-table';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/react/src/components/ui/table';

import { Skeleton } from '@/react/src/components/ui/skeleton';

import { DataTablePagination } from './data-table-pagination';
// import { DataTableToolbar } from './data-table-toolbar';
import { useEffect } from 'react';

const getRandomWidth = (min = 100, max = 250) => {
  return Math.floor(Math.random() * (max - min + 1)) + min;
};

export function DataTable({
  columns,
  data,
  error,
  isLoading,
  isFetching,
  setPage,
  perPage,
  setPerPage,
}) {
  const [rowSelection, setRowSelection] = React.useState({});
  const [columnVisibility, setColumnVisibility] = React.useState({});
  const [columnFilters, setColumnFilters] = React.useState([]);
  const [sorting, setSorting] = React.useState([]);
  const [pagination, setPagination] = React.useState({
    pageIndex: 0,
    pageSize: perPage,
  });

  const table = useReactTable({
    data: data?.data,
    columns,
    rowCount: data ? data.total : 0,
    state: {
      sorting,
      columnVisibility,
      rowSelection,
      columnFilters,
      isLoading,
      error,
      isFetching,
      pagination,
    },
    enableRowSelection: true,
    manualPagination: true,
    onRowSelectionChange: setRowSelection,
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onColumnVisibilityChange: setColumnVisibility,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
  });

  // console.log(typeof data === 'undefined' ? 'otin' : data);

  useEffect(() => {
    setPage(pagination.pageIndex + 1);
    setPerPage(pagination.pageSize);
  }, [pagination]);

  const SkellyBody = ({ length = 10 }) => {
    return [...Array(length).keys()].map(() => (
      <TableRow>
        {[...Array(columns.length).keys()].map((x) => (
          <TableCell key={x}>
            <Skeleton className={`h-[25px] w-[${getRandomWidth}px]`} />
          </TableCell>
        ))}
      </TableRow>
    ));
  };

  return (
    <div className='space-y-4'>
      {/* <DataTableToolbar table={table} /> */}
      <div className='rounded-md border'>
        <Table>
          <TableHeader>
            {table.getHeaderGroups().map((headerGroup) => (
              <TableRow key={headerGroup.id}>
                {headerGroup.headers.map((header) => {
                  return (
                    <TableHead key={header.id} colSpan={header.colSpan}>
                      {header.isPlaceholder
                        ? null
                        : flexRender(
                            header.column.columnDef.header,
                            header.getContext()
                          )}
                    </TableHead>
                  );
                })}
              </TableRow>
            ))}
          </TableHeader>
          {isLoading || isFetching || typeof data === 'undefined' ? (
            <TableBody>
              {/* <TableRow>
                <TableCell
                  colSpan={columns.length}
                  className="h-24 text-center"
                >
                  Loading...
                </TableCell>
              </TableRow> */}
              <SkellyBody length={perPage} />
            </TableBody>
          ) : error ? (
            <TableBody>
              <TableRow>
                <TableCell
                  colSpan={columns.length}
                  className='h-24 text-center'
                >
                  Error: {error.message}
                </TableCell>
              </TableRow>
            </TableBody>
          ) : (
            <TableBody>
              {table.getRowModel().rows?.length ? (
                table.getRowModel().rows.map((row) => (
                  <TableRow
                    key={row.id}
                    data-state={row.original.status === 'Pending' && 'selected'}
                    // className={row.original.status == "Pending" && "font-bold"}
                  >
                    {row.getVisibleCells().map((cell) => (
                      <TableCell key={cell.id}>
                        {flexRender(
                          cell.column.columnDef.cell,
                          cell.getContext()
                        )}
                      </TableCell>
                    ))}
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell
                    colSpan={columns.length}
                    className='h-24 text-center'
                  >
                    No results.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          )}
        </Table>
      </div>
      {!isLoading && <DataTablePagination table={table} data={data} />}
    </div>
  );
}
