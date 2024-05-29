import React from 'react';
import { useUsersData } from './data';
import { DataTable } from './data-table';
import { columns } from './columns';

const ActiveUsers = () => {
  const { data, error, isLoading, isFetching, setPage, perPage, setPerPage } =
    useUsersData({
      status: 'active',
    });

  return (
    <DataTable
      data={data}
      columns={columns}
      error={error}
      isLoading={isLoading}
      isFetching={isFetching}
      setPage={setPage}
      perPage={perPage}
      setPerPage={setPerPage}
    />
  );
};

export default ActiveUsers;
