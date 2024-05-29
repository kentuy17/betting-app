import { cn } from '@/lib/utils';
import { CheckIcon, Cross1Icon } from '@radix-ui/react-icons';

import { Badge } from '@/react/src/components/ui/badge';
import { Button } from '@/react/src/components/ui/button';

import { DataTableColumnHeader } from './data-table-column-header';

import moment from 'moment';
import { DataTableRowActions } from './data-table-row-actions';

const ButtonComponent = ({ row }) => {
  const handleReject = (row) => {
    console.log(row, 'reject');
  };

  return (
    <div className="flex">
      <Button size="3" variant="outline" onClick={() => console.log('clicked')}>
        <CheckIcon className="h-4 w-4" color="green" />
      </Button>
      <Button size="3" variant="outline" onClick={() => handleReject(row)}>
        <Cross1Icon className="h-4 w-4" color="red" />
      </Button>
    </div>
  );
};

export const columns = [
  {
    accessorKey: 'id',
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="ID" />
    ),
    cell: ({ row }) => <div className="w-[80px]">{row.getValue('id')}</div>,
  },
  {
    accessorKey: 'username',
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Username" />
    ),
    cell: ({ row }) => {
      return (
        <div className="flex space-x-2">
          <span className="max-w-[500px] truncate">
            {row.getValue('username')}
          </span>
        </div>
      );
    },
  },
  {
    accessorKey: 'points',
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Points" />
    ),
    cell: ({ row }) => {
      return (
        <div className="flex space-x-2 flex-row-reverse">
          <span className="max-w-[500px] truncate">
            {parseFloat(row.getValue('points')).toFixed(2)}
          </span>
        </div>
      );
    },
    // filterFn: (row, id, value) => {
    //   return value.includes(row.getValue(id));
    // },
  },
  // {
  //   accessorKey: 'updated_at',
  //   header: ({ column }) => (
  //     <DataTableColumnHeader column={column} title="Last Updated" />
  //   ),
  //   cell: ({ row }) => {
  //     return (
  //       <div className="flex items-center">
  //         <span>{moment(row.getValue('updated_at')).fromNow()}</span>
  //       </div>
  //     );
  //   },
  //   // filterFn: (row, id, value) => {
  //   //   return value.includes(row.getValue(id));
  //   // },
  // },
  // {
  //   id: 'type',
  //   header: ({ column }) => (
  //     <DataTableColumnHeader column={column} title="Action" />
  //   ),
  //   cell: ({ row }) => {
  //     return (
  //       <div className="flex items-center max-w-3">
  //         <span>{row.getValue('type')}</span>
  //       </div>
  //     );
  //   },
  //   filterFn: (row, id, value) => {
  //     return value.includes(row.getValue(id));
  //   },
  // },
  {
    id: 'actions',
    cell: ({ row }) => <DataTableRowActions row={row} />,
  },
];
