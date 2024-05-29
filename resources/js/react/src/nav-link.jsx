import {
  Coins,
  CoinsIcon,
  HandCoinsIcon,
  Home,
  LineChart,
  Package,
  Package2,
  ShoppingCart,
  Users2,
} from 'lucide-react';
// import { Link } from 'react-router-dom';
import { cn } from '../../lib/utils';
import { Button, buttonVariants } from '@/react/src/components/ui/button';
import { Badge } from '@/react/src/components/ui/badge';
import { Money } from '@mui/icons-material';

const NavLink = () => {
  const { pathname } = window.location;

  const handleOnClick = () => {
    window.location.href = '/';
  };

  const links = [
    {
      href: '/landing',
      icon: <Home />,
      label: 'LANDING',
    },
    {
      href: '/users-list',
      icon: <Users2 />,
      label: 'USERS',
    },
    {
      href: '/transactions',
      icon: <HandCoinsIcon />,
      label: 'CI/CO',
    },
    {
      href: '/event',
      icon: <Package />,
      label: 'EVENT',
    },
    {
      href: '/summary-bet',
      icon: <LineChart />,
      label: 'BET-SUMMARY',
    },
  ];

  return (
    <nav className="space-y-1 gap-2">
      {links.map((item) => (
        <a key={item.label} href={item.href}>
          <Button
            onClick={handleOnClick}
            variant={pathname.includes(item.href) ? 'default' : 'ghost'}
            className={cn(
              buttonVariants({
                variant: pathname.includes(item.href) ? 'default' : 'ghost',
                size: 'icon',
              }),
              'w-full justify-start gap-[1rem]',
              pathname.includes(item.href) &&
                'dark:bg-muted dark:text-muted-foreground dark:hover:bg-muted dark:hover:text-white',
            )}
          >
            {item.icon}
            <span
              className={
                pathname.includes(item.href) ? 'font-semibold' : 'font-medium'
              }
            >
              {item.label}
            </span>
            {item.notif && (
              <Badge className="ml-auto flex h-6 w-6 shrink-0 items-center justify-center rounded-full">
                {item.notif}
              </Badge>
            )}
          </Button>
        </a>
      ))}
      {/* <Link
        href="#"
        onClick={() => {
          window.location.href = '/';
        }}
        className="group flex h-10 w-10 shrink-0 items-center justify-center gap-2 rounded-full bg-primary text-lg font-semibold text-primary-foreground md:text-base"
      >
        <Package2 className="h-5 w-5 transition-all group-hover:scale-110" />
        <span className="sr-only">SABONG</span>
      </Link>
      <Link
        href="#"
        className="flex items-center gap-4 px-2.5 text-muted-foreground hover:text-foreground"
      >
        <Home className="h-5 w-5" />
        Landing
      </Link>
      <Link
        href="#"
        className="flex items-center gap-4 px-2.5 text-muted-foreground hover:text-foreground"
      >
        <ShoppingCart className="h-5 w-5" />
        Orders
      </Link>
      <Link href="#" className="flex items-center gap-4 px-2.5 text-foreground">
        <Package className="h-5 w-5" />
        Products
      </Link>
      <Link
        href="#"
        className={cn(
          active,
          'flex items-center gap-4 px-2.5 text-muted-foreground hover:text-foreground',
        )}
      >
        <Users2 className="h-5 w-5" />
        Users
      </Link>
      <Link
        href="#"
        className="flex items-center gap-4 px-2.5 text-muted-foreground hover:text-foreground"
      >
        <LineChart className="h-5 w-5" />
        Settings
      </Link> */}
    </nav>
  );
};

export default NavLink;
