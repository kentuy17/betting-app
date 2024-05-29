import { useQuery } from '@tanstack/react-query';
import { useState } from 'react';

export function useUsersData({ status }) {
  const [columnFilters, setColumnFilters] = useState([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [pagination, setPagination] = useState({
    pageIndex: 0,
    pageSize: 10,
  });
  const [sorting, setSorting] = useState([]);
  const [fakeData, setFakeData] = useState({});

  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);

  const url =
    process.env.NODE_ENV === 'production'
      ? 'https://isp24.live'
      : 'http://127.0.0.1:8000';

  const { data, isError, isFetching, isLoading, refetch } = useQuery({
    queryKey: ['active-users', page, perPage],
    queryFn: async () => {
      const fetchURL = new URL('/users-data', url);

      fetchURL.searchParams.set('page', page);

      if (perPage) {
        fetchURL.searchParams.set('per_page', perPage);
      }

      const response = await fetch(fetchURL.href);
      const json = await response.json();
      setFakeData(json);
      return json;
    },
    keepPreviousData: true,
  });

  return {
    data,
    isError,
    isFetching,
    isLoading,
    refetch,
    columnFilters,
    setColumnFilters,
    globalFilter,
    setGlobalFilter,
    pagination,
    setPagination,
    sorting,
    setSorting,
    fakeData,
    setPage,
    perPage,
    setPerPage,
  };
}
