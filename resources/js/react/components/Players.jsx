import { useCallback, useEffect, useMemo, useState } from 'react';
import { MaterialReactTable } from 'material-react-table';
import { Box, IconButton, Tooltip, Typography, Skeleton } from '@mui/material';
import RefreshIcon from '@mui/icons-material/Refresh';
import CurrencyRubleIcon from '@mui/icons-material/CurrencyRuble';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import {
  QueryClient,
  QueryClientProvider,
  useMutation,
  useQuery,
} from '@tanstack/react-query';
import { Add, Delete, Edit } from '@mui/icons-material';
import Button from '@mui/material/Button';
import DeleteIcon from '@mui/icons-material/Delete';
import { axios } from '@bundled-es-modules/axios';

const queryClient = new QueryClient();

const Example = () => {
  const [columnFilters, setColumnFilters] = useState([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [sorting, setSorting] = useState([]);
  const [fakeData, setFakeData] = useState({});
  const [points, setPoints] = useState(0);
  const [pagination, setPagination] = useState({
    pageIndex: 0,
    pageSize: 10,
  });

  const formatMoney = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  };

  const refreshPoints = () => {
    axios.get('/user/points').then((res) => {
      setPoints(res.data.points);
    });
  };

  useEffect(() => {
    axios.get('/user/points').then((res) => {
      setPoints(res.data.points);
    });
  }, []);

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
          ? 'https://isp24.live'
          : 'http://127.0.0.1:8000',
      );
      fetchURL.searchParams.set(
        'start',
        `${pagination.pageIndex * pagination.pageSize}`,
      );
      fetchURL.searchParams.set('size', `${pagination.pageSize}`);
      fetchURL.searchParams.set('filters', JSON.stringify(columnFilters ?? []));
      fetchURL.searchParams.set('globalFilter', globalFilter ?? '');
      fetchURL.searchParams.set('sorting', JSON.stringify(sorting ?? []));

      const response = await fetch(fetchURL.href);
      const json = await response.json();
      setFakeData(json);
      return json;
    },
    keepPreviousData: true,
  });

  const getRandomInt = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
  };

  const mutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.post('/master-agent/topup', data);
      refreshPoints();
      return response;
    },
    onSuccess: async (result, variables, context) => {
      await queryClient.setQueryData(['table-data'], result.data);
      setFakeData(result.data);
      alert('Points successfully added!');
    },
    onError: (err) => alert(JSON.stringify(err.message)),
  });

  const handleAddPoints = (userId) => {
    try {
      let amountToAdd = prompt('Enter Amount to Loadi: ', 0);
      if (amountToAdd === null) return;

      if (amountToAdd > points) {
        alert('Amount exceeds points!');
        return;
      }

      if (amountToAdd < 0) {
        alert('Amount cannot be negative!');
        return;
      }

      mutation.mutate({
        userId: userId,
        amount: amountToAdd,
      });
    } catch (error) {
      console.log(error);
      alert('Oops! something went wrong!');
    }
  };

  const columns = useMemo(
    () => [
      {
        accessorKey: 'user.username',
        header: 'Usermame',
        size: 18,
      },
      {
        accessorKey: 'user.points',
        header: 'Points',
        size: 18,
        muiTableBodyCellProps: () => {
          return {
            style: {
              paddingLeft: 0,
              paddingRight: 10,
            },
          };
        },
        Cell: ({ _, row }) =>
          row.original.user !== undefined ? (
            <Box
              display="flex"
              pl="0"
              justifyContent="flex-start"
              style={{
                marginLeft: 0,
                marginRight: 20,
                padding: 0,
                display: 'flex',
                justifyContent: 'space-between',
              }}
            >
              <Tooltip arrow placement="left" title="Add Points">
                <IconButton
                  // disabled={true}
                  onClick={() => handleAddPoints(row.original?.user?.id)}
                  // onClick={() => alert('Temporaryly disabled')}
                >
                  <Add sx={{ color: '#0d6efd' }} />
                </IconButton>
              </Tooltip>
              <Typography align="right" sx={{ color: 'yellow', mt: 1 }}>
                {parseFloat(
                  row.original.user !== undefined
                    ? row.original.user.points
                    : '0',
                ).toFixed(2)}
              </Typography>
            </Box>
          ) : (
            <Skeleton width={getRandomInt(20, 50)} animation="wave" />
          ),
      },
      {
        accessorKey: 'type',
        header: 'Type',
        size: 18,
        accessorFn: (row) => {
          return 'Player';
        },
      },
      {
        accessorKey: 'agent_commission.commission',
        header: 'Commission',
        size: 18,
        accessorFn: (row) => {
          if (row.agent_commission == null) return '0.00';
          return row.agent_commission !== undefined ? (
            formatMoney(parseFloat(row.agent_commission.commission).toFixed(2))
          ) : (
            <Skeleton width={getRandomInt(20, 50)} animation="wave" />
          );
        },
      },
    ],
    [points],
  );

  const theme = useMemo(
    () =>
      createTheme({
        palette: { mode: 'dark' },
      }),
    [],
  );

  return (
    <ThemeProvider theme={theme}>
      <MaterialReactTable
        columns={columns}
        data={fakeData?.data ?? []} //data is undefined on first render
        initialState={{ showColumnFilters: false, density: 'compact' }}
        muiTableBodyCellProps={() => {
          return {
            sx: {
              borderRight: '1px solid #f0f0f0',
            },
          };
        }}
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
          <Tooltip arrow placement="right" title="Current Points">
            <Button
              color="warning"
              variant="outlined"
              startIcon={<CurrencyRubleIcon />}
            >
              {formatMoney(points)}
            </Button>
          </Tooltip>
        )}
        rowCount={fakeData?.total ?? 0}
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

const ExampleWithReactQueryProvider = () => (
  <QueryClientProvider client={queryClient}>
    <Example />
  </QueryClientProvider>
);

export default ExampleWithReactQueryProvider;
