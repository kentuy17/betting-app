import { cn } from "@/lib/utils";
import { DataTableColumnHeader } from "./data-table-column-header";
import { Badge } from "@ui/badge";
import {
  ArrowDownIcon,
  ArrowRightIcon,
  ArrowUpIcon,
  CheckCircledIcon,
  CheckIcon,
  Cross1Icon,
  CrossCircledIcon,
  QuestionMarkCircledIcon,
} from "@radix-ui/react-icons";
import { Button } from "@/components/ui/button";
import axiosClient from '@/lib/axios-client';
import { useNavigate } from 'react-router-dom';

const labels = [
  {
    value: "bug",
    label: "Bug",
  },
  {
    value: "feature",
    label: "Feature",
  },
  {
    value: "documentation",
    label: "Documentation",
  },
];

export const statuses = [
  {
    value: "Approved",
    label: "Approved",
    icon: CheckCircledIcon,
  },
  {
    value: "Pending",
    label: "Pending",
    icon: QuestionMarkCircledIcon,
  },
  {
    value: "Rejected",
    label: "Rejected",
    icon: CrossCircledIcon,
  },
];

export const priorities = [
  {
    label: "Low",
    value: "low",
    icon: ArrowDownIcon,
  },
  {
    label: "Medium",
    value: "medium",
    icon: ArrowRightIcon,
  },
  {
    label: "High",
    value: "high",
    icon: ArrowUpIcon,
  },
];



const getIconColor = (value) => {
  switch (value) {
    case "Approved":
      return "text-green-500";
    case "Pending":
      return "text-yellow-500";
    case "Rejected":
      return "text-red-500";
    default:
      return "text-gray-500";
  }
};

const ButtonComponent = ({ row }) => {
  const navigate = useNavigate()

  const handleApprove = (row) => {
    axiosClient
      .post("/user-approval/update", {
        status: "Approved",
        id: row.original.id,
      })
      .then((res) => {
        navigate('/employees')
      })
      .catch((err) => console.log(err));
  };

  const handleReject = (row) => {
    console.log(row, "reject");
  };

  return (<div className="flex">
    <Button size="3" variant="outline" onClick={() => handleApprove(row)}>
      <CheckIcon className="h-4 w-4" color="green" />
    </Button>
    <Button size="3" variant="outline" onClick={() => handleReject(row)}>
      <Cross1Icon className="h-4 w-4" color="red" />
    </Button>
  </div>)
}

export const columns = [
  {
    accessorKey: "employeeId",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Employee ID" />
    ),
    cell: ({ row }) => (
      <div className="w-[80px]">{row.getValue("employeeId")}</div>
    ),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
    cell: ({ row }) => {
      const label = labels.find((label) => label.value === row.original.label);

      return (
        <div className="flex space-x-2">
          {label && <Badge variant="outline">{label.label}</Badge>}
          <span className="max-w-[500px] truncate">{row.getValue("name")}</span>
        </div>
      );
    },
  },
  {
    accessorKey: "status",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Approval Status" />
    ),
    cell: ({ row }) => {
      const status = statuses.find(
        (status) => status.value === row.getValue("status")
      );

      if (!status) {
        return null;
      }

      return (
        <div className="flex w-[100px] items-center">
          {status.icon && (
            <status.icon
              className={cn(
                "mr-2 h-4 w-4 text-muted-foreground",
                getIconColor(status.value)
              )}
            />
          )}
          <span>{status.label}</span>
        </div>
      );
    },
    filterFn: (row, id, value) => {
      return value.includes(row.getValue(id));
    },
  },
  {
    accessorKey: "requestedOn",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Request Date" />
    ),
    cell: ({ row }) => {
      return (
        <div className="flex items-center">
          <span>{row.getValue("requestedOn")}</span>
        </div>
      );
    },
    filterFn: (row, id, value) => {
      return value.includes(row.getValue(id));
    },
  },
  {
    id: "actions",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Action" />
    ),
    cell: ({ row }) => {
      return <ButtonComponent row={row} />;
    },
  },
];
