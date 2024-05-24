import { useEffect, useMemo, useState } from 'react';
import { MaterialReactTable } from 'material-react-table';
import {
  Box,
  IconButton,
  Tooltip,
  Typography,
  Skeleton,
  MenuItem,
  Modal,
  FormControl,
  Select,
  InputLabel,
  Slider,
} from '@mui/material';
import CurrencyRubleIcon from '@mui/icons-material/CurrencyRuble';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import {
  QueryClient,
  QueryClientProvider,
  useMutation,
  useQuery,
} from '@tanstack/react-query';
import { Add, Edit } from '@mui/icons-material';
import Button from '@mui/material/Button';
import { axios } from '@bundled-es-modules/axios';

const queryClient = new QueryClient();

const PlayersTable = () => {
  const [columnFilters, setColumnFilters] = useState([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [sorting, setSorting] = useState([]);
  const [fakeData, setFakeData] = useState({});
  const [points, setPoints] = useState(0);
  const [pagination, setPagination] = useState({
    pageIndex: 0,
    pageSize: 10,
  });

  const [player, setPlayer] = useState({
    type: 'player',
    user_id: '',
    percent: 2,
  });

  const [open, setOpen] = useState(false);

  const handleEditRow = (row) => {
    let agentType = row.sub_agent ? row.sub_agent.type : 'player';
    let percent = row.sub_agent ? row.sub_agent.percent : 0;
    setOpen(true);
    setPlayer({
      user_id: row.user_id,
      type: agentType,
      percent: percent,
    });
  };

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
  });

  const handleAddPoints = (userId) => {
    try {
      let amountToAdd = prompt('Enter Amount to Load: ', 0);
      if (amountToAdd === null) return;

      let isNum = /^\d+$/.test(amountToAdd);
      if (!isNum) {
        alert('Enter only numbers!');
        return;
      }

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

  const types = [
    { value: 'sub-agent', label: 'Sub-agent' },
    { value: 'player', label: 'Player' },
  ];

  const columns = useMemo(
    () => [
      {
        accessorKey: 'user.username',
        header: 'Usermame',
        enableEditing: false,
        size: 18,
      },
      {
        accessorKey: 'user.points',
        header: 'Points',
        enableEditing: false,
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
                  onClick={() => handleAddPoints(row.original?.user?.id)}
                >
                  <Add sx={{ color: '#0d6efd' }} />
                </IconButton>
              </Tooltip>
              <Typography align="right" sx={{ color: 'yellow', mt: 1 }}>
                {parseFloat(
                  row.original.user !== undefined
                    ? row.original.user.points
                    : '0'
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
        muiTableBodyCellEditTextFieldProps: {
          select: true, //change to select for a dropdown
          children: types.map((type) => (
            <MenuItem key={type.value} value={type.value}>
              {type.label}
            </MenuItem>
          )),
        },
        accessorFn: (row) => {
          return row.sub_agent?.type ? row.sub_agent.type : 'player';
        },
      },
      {
        accessorKey: 'agent_commission.commission',
        enableEditing: false,
        header: 'Comm',
        size: 10,
        accessorFn: (row) => {
          if (row.agent_commission == null) return '0.00';
          return row.agent_commission !== undefined ? (
            formatMoney(parseFloat(row.agent_commission.commission).toFixed(2))
          ) : (
            <Skeleton width={getRandomInt(20, 50)} animation="wave" />
          );
        },
      },
      {
        accessorKey: 'sub_agent.percent',
        enableEditing: true,
        header: 'Percent',
        size: 10,
        accessorFn: (row) => {
          if (row.sub_agent == null) return '0 %';
          return row.sub_agent !== undefined ? (
            `${row.sub_agent.percent} %`
          ) : (
            <Skeleton width={getRandomInt(20, 50)} animation="wave" />
          );
        },
      },
    ],
    [points]
  );

  const theme = useMemo(
    () =>
      createTheme({
        palette: { mode: 'dark' },
      }),
    []
  );

  const handleSaveRowEdits = async () => {
    try {
      let tmp = [...fakeData.data];
      let index = tmp.findIndex((item) => item.user_id == player.user_id);
      tmp[index].sub_agent = { type: player.type, percent: player.percent };

      await axios.post('/master-agent/update-type', {
        user_id: player.user_id,
        type: player.type,
        percent: player.percent,
      });
      setFakeData({ ...fakeData, data: tmp });
      setOpen(false);
    } catch (error) {
      alert(error.response.data.error);
    }
  };

  const style = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    width: 300,
    bgcolor: 'background.paper',
    border: '2px solid #000',
    boxShadow: 24,
    p: 4,
    color: 'white',
  };

  return (
    <ThemeProvider theme={theme}>
      <MaterialReactTable
        columns={columns}
        data={fakeData?.data ?? []} //data is undefined on first render
        enableEditing={true}
        initialState={{ showColumnFilters: false, density: 'compact' }}
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
        renderRowActions={({ row, table }) => (
          <Box sx={{ display: 'flex', gap: '1rem' }}>
            <Tooltip arrow placement="left" title="Edit">
              <IconButton onClick={() => handleEditRow(row.original)}>
                <Edit />
              </IconButton>
            </Tooltip>
          </Box>
        )}
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
      <Modal
        open={open}
        onClose={() => setOpen(false)}
        aria-labelledby="modal-modal-title"
        aria-describedby="modal-modal-description"
      >
        <Box sx={style}>
          <Typography
            id="modal-modal-title"
            variant="h6"
            component="h2"
            sx={{ marginBottom: 2 }}
          >
            Edit Item
          </Typography>
          <FormControl fullWidth>
            <InputLabel id="demo-simple-select-label">Type</InputLabel>
            <Select
              labelId="demo-simple-select-label"
              id="demo-simple-select"
              value={player.type}
              label="Type"
              onChange={(e) => setPlayer({ ...player, type: e.target.value })}
            >
              {types.map((type) => (
                <MenuItem key={type.value} value={type.value}>
                  {type.label}
                </MenuItem>
              ))}
            </Select>
          </FormControl>
          <FormControl sx={{ marginTop: 2 }} fullWidth>
            <Typography id="input-slider" gutterBottom>
              Percent
            </Typography>
            <Slider
              aria-label="Percent"
              value={player.percent}
              valueLabelDisplay={'on'}
              step={0.5}
              marks
              min={0}
              max={4}
              disabled={player.type == 'player'}
              onChange={(e) =>
                setPlayer({ ...player, percent: e.target.value })
              }
            />
          </FormControl>
          <FormControl
            sx={{ display: 'flex', justifyContent: 'flex-end', marginTop: 2 }}
          >
            <Button variant="contained" onClick={handleSaveRowEdits}>
              Save
            </Button>
          </FormControl>
        </Box>
      </Modal>
    </ThemeProvider>
  );
};

const MastersPlayers = () => (
  <QueryClientProvider client={queryClient}>
    <PlayersTable />
  </QueryClientProvider>
);

export default MastersPlayers;
