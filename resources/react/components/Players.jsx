import React, { useMemo, useState } from 'react';
import { MaterialReactTable } from 'material-react-table';
import { Box, IconButton, Tooltip, Typography, Skeleton } from '@mui/material';
import RefreshIcon from '@mui/icons-material/Refresh';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import {
  QueryClient,
  QueryClientProvider,
  useQuery,
} from '@tanstack/react-query';
import { Add, Delete, Edit } from '@mui/icons-material';

const Example = () => {
  const [columnFilters, setColumnFilters] = useState([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [sorting, setSorting] = useState([]);
  const [pagination, setPagination] = useState({
    pageIndex: 0,
    pageSize: 10,
  });

  const { data, isError, isFetching, isLoading, refetch } = useQuery({
    queryKey: [
      'table-data',
      columnFilters, //refetch when columnFilters changes
      globalFilter, //refetch when globalFilter changes
      pagination.pageIndex, //refetch when pagination.pageIndex changes
      pagination.pageSize, //refetch when pagination.pageSize changes
      sorting, //refetch when sorting changes
    ],
    queryFn: async () => {
      const fetchURL = new URL(
        '/master-agent/player-list',
        process.env.NODE_ENV === 'production'
          ? 'https://www.material-react-table.com'
          : 'http://127.0.0.1:8006'
      );
      fetchURL.searchParams.set(
        'start',
        `${pagination.pageIndex * pagination.pageSize}`
      );
      fetchURL.searchParams.set('size', `${pagination.pageSize}`);
      fetchURL.searchParams.set('filters', JSON.stringify(columnFilters ?? []));
      fetchURL.searchParams.set('globalFilter', globalFilter ?? '');
      fetchURL.searchParams.set('sorting', JSON.stringify(sorting ?? []));

      const response = await fetch(fetchURL.href);
      const json = await response.json();
      return json;
    },
    keepPreviousData: true,
  });

  const getRandomInt = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
  };

  const formatter = (value) => {
    let points = value?.user?.points;
    let convert =
      typeof points == 'undefined' || points == '0'
        ? '0.00'
        : new Intl.NumberFormat('en-US').format(points.toFixed(2));
    return typeof points == 'undefined' ? (
      <Skeleton width={getRandomInt(20, 50)} animation="wave" />
    ) : (
      <Box display="flex">
        <Tooltip arrow placement="left" title="Add Points">
          <IconButton onClick={() => prompt('Enter Amount to Add: ', 0)}>
            <Add sx={{ color: '#0d6efd' }} />
          </IconButton>
        </Tooltip>
        <Typography sx={{ color: 'yellow', mt: 1 }}>{convert}</Typography>
      </Box>
    );
  };

  const columns = useMemo(
    () => [
      {
        accessorKey: 'user.username',
        header: 'Usermame',
      },
      {
        accessorKey: 'user.points',
        header: 'Points',
        accessorFn: (row) => {
          return formatter(row);
        },
      },
      {
        accessorKey: 'created_at',
        header: 'Created',
      },
      {
        accessorKey: 'updated_at',
        header: 'Updated',
      },
    ],
    []
  );

  const theme = useMemo(
    () =>
      createTheme({
        palette: { mode: 'dark' },
      }),
    []
  );

  return (
    <ThemeProvider theme={theme}>
      <MaterialReactTable
        columns={columns}
        data={data?.data ?? []} //data is undefined on first render
        initialState={{ showColumnFilters: false }}
        manualFiltering
        manualPagination
        manualSorting
        muiToolbarAlertBannerProps={
          isError
            ? { color: 'error', children: 'Error loading data' }
            : undefined
        }
        onColumnFiltersChange={setColumnFilters}
        onGlobalFilterChange={setGlobalFilter}
        onPaginationChange={setPagination}
        onSortingChange={setSorting}
        renderTopToolbarCustomActions={() => (
          <Tooltip arrow title="Refresh Data">
            <IconButton onClick={() => refetch()}>
              <RefreshIcon />
            </IconButton>
          </Tooltip>
        )}
        rowCount={data?.total ?? 0}
        state={{
          columnFilters,
          globalFilter,
          isLoading,
          pagination,
          showAlertBanner: isError,
          showProgressBars: isFetching,
          sorting,
        }}
      />
    </ThemeProvider>
  );
};

const queryClient = new QueryClient();

const ExampleWithReactQueryProvider = () => (
  <QueryClientProvider client={queryClient}>
    <Example />
  </QueryClientProvider>
);

export default ExampleWithReactQueryProvider;
