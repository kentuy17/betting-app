import { Link } from 'react-router-dom';
import {
  File,
  Home,
  LineChart,
  ListFilter,
  MoreHorizontal,
  Package,
  Package2,
  PanelLeft,
  PlusCircle,
  Search,
  Settings,
  ShoppingCart,
  Users2,
} from 'lucide-react';

import { Badge } from '../src/components/ui/badge';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '../src/components/ui/breadcrumb';
import { Button } from '../src/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '../src/components/ui/card';
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '../src/components/ui/dropdown-menu';
import { Input } from '../src/components/ui/input';
import { Sheet, SheetContent, SheetTrigger } from '../src/components/ui/sheet';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/react/src/components/ui/table';
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/react/src/components/ui/tabs';
import NavLink from '@/react/src/nav-link';
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/react/src/components/ui/avatar';

// import { useUsersData } from '@/react/src/user-list/data';
import AllUsers from '@/react/src/user-list/all-users';

import ActiveUsers from '@/react/src/user-list/active-users';

const PseudoDashboard = ({ user = 'Admin' }) => {
  // const { data, isError, isFetching, isLoading } = useUsersData();

  // if (isError) {
  //   return <div>Error: {data.message}</div>;
  // }

  // if (isLoading || isFetching) {
  //   return <div>Loading...</div>;
  // }
  // console.log(data);

  return (
    <div className="flex min-h-screen w-full flex-col bg-muted/40">
      <aside className="fixed inset-y-0 left-0 z-10 hidden w-14 flex-col border-r bg-background sm:flex">
        <nav className="flex flex-col items-center gap-4 px-2 py-4">
          <Link
            href="#"
            className="group flex h-9 w-9 shrink-0 items-center justify-center gap-2 rounded-full bg-primary text-lg font-semibold text-primary-foreground md:h-8 md:w-8 md:text-base"
          >
            <Package2 className="h-4 w-4 transition-all group-hover:scale-110" />
            <span className="sr-only">Sabong</span>
          </Link>
          <Link
            href="#"
            className="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
          >
            <Home className="h-5 w-5" />
            <span className="sr-only">Dashboard</span>
          </Link>
          <Link
            href="#"
            className="flex h-9 w-9 items-center justify-center rounded-lg transition-colors hover:text-foreground md:h-8 md:w-8"
          >
            <ShoppingCart className="h-5 w-5" />
            <span className="sr-only">Orders</span>
          </Link>
          <Link
            href="#"
            className="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-accent-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
          >
            <Package className="h-5 w-5" />
            <span className="sr-only">Products</span>
          </Link>
          <Link
            href="#"
            className="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
          >
            <Users2 className="h-5 w-5" />
            <span className="sr-only">Customers</span>
          </Link>
          <Link
            href="#"
            className="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
          >
            <LineChart className="h-5 w-5" />
            <span className="sr-only">Analytics</span>
          </Link>
        </nav>
        <nav className="mt-auto flex flex-col items-center gap-4 px-2 py-4">
          <Link
            href="#"
            className="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
          >
            <Settings className="h-5 w-5" />
            <span className="sr-only">Settings</span>
          </Link>
        </nav>
      </aside>
      <div className="flex flex-col sm:gap-4 sm:py-4 sm:pl-14">
        <header className="sticky top-0 z-30 flex h-14 items-center gap-4 border-b bg-background px-4 sm:static sm:h-auto sm:border-0 sm:bg-transparent sm:px-6">
          <Sheet>
            <SheetTrigger asChild>
              <Button size="icon" variant="outline" className="sm:hidden">
                <PanelLeft className="h-5 w-5" />
                <span className="sr-only">Toggle Menu</span>
              </Button>
            </SheetTrigger>
            <SheetContent side="left" className="sm:max-w-xs">
              <NavLink />
            </SheetContent>
          </Sheet>
          <Breadcrumb className="hidden md:flex">
            <BreadcrumbList>
              <BreadcrumbItem>
                <BreadcrumbLink asChild>
                  <Link href="#">Dashboard</Link>
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbLink asChild>
                  <Link href="#">Products</Link>
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbPage>All Users</BreadcrumbPage>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>
          <div className="relative ml-auto flex-1 md:grow-0">
            <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input
              type="search"
              placeholder="Search..."
              className="w-full rounded-lg bg-background pl-8 md:w-[200px] lg:w-[320px]"
            />
          </div>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Avatar className="h-9 w-9 border-black-500 border cursor-pointer">
                <AvatarImage
                  src="/img/sabong-aficionado-icon-43px.png"
                  alt="Aficionado"
                />
                <AvatarFallback>
                  {/* {localStorage.getItem("SESSION_NAME").slice(0, 1)} */}
                  {user.slice(0, 1)}
                </AvatarFallback>
              </Avatar>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>{user}</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuItem className="cursor-pointer">
                Profile
              </DropdownMenuItem>
              <DropdownMenuItem disabled={true}>Settings</DropdownMenuItem>
              <DropdownMenuItem className="cursor-pointer">
                Support
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem className="cursor-pointer">
                Logout
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </header>
        <main className="grid flex-1 items-start gap-4 p-4 sm:px-6 sm:py-0 md:gap-8">
          <Tabs defaultValue="active">
            <div className="flex items-center">
              <TabsList>
                <TabsTrigger value="all">All</TabsTrigger>
                <TabsTrigger value="active">Active</TabsTrigger>
                <TabsTrigger value="staff">Staff</TabsTrigger>
                <TabsTrigger value="agent">Agent</TabsTrigger>
              </TabsList>
              <div className="ml-auto flex items-center gap-2">
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="outline" size="sm" className="h-7 gap-1">
                      <ListFilter className="h-3.5 w-3.5" />
                      <span className="sr-only sm:not-sr-only sm:whitespace-nowrap">
                        Filter
                      </span>
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Order by</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuCheckboxItem checked>
                      Activity
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem>Points</DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem>Name</DropdownMenuCheckboxItem>
                  </DropdownMenuContent>
                </DropdownMenu>
                <Button size="sm" variant="outline" className="h-7 gap-1">
                  <File className="h-3.5 w-3.5" />
                  <span className="sr-only sm:not-sr-only sm:whitespace-nowrap">
                    Export
                  </span>
                </Button>
                <Button size="sm" className="h-7 gap-1">
                  <PlusCircle className="h-3.5 w-3.5" />
                  <span className="sr-only sm:not-sr-only sm:whitespace-nowrap">
                    Add Product
                  </span>
                </Button>
              </div>
            </div>
            <TabsContent value="all">
              <AllUsers status={'all'} />
            </TabsContent>
            <TabsContent value="active">
              <ActiveUsers />
            </TabsContent>
          </Tabs>
        </main>
      </div>
    </div>
  );
};

export default PseudoDashboard;
