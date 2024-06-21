import { Tooltip } from '@mui/material';
import { useEffect, useState } from 'react';

const CardCommission = ({
  amount,
  title,
  linkText,
  icon,
  onClick,
  tooltip = '',
  linkIcon,
}) => {
  // const handleClick = () => {
  //   onClick();
  // };

  const WrenchIcon = () => {
    return <i className="fa-solid fa-wrench mr-1"></i>;
  };

  const [value, setValue] = useState(0);
  const [iconLink, setIconLink] = useState(<WrenchIcon />);

  const formatMoney = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  };

  useEffect(() => {
    let tmpAmount =
      typeof amount === 'number' ? `₱ ${formatMoney(amount)}` : amount;

    if (linkIcon) {
      setIconLink(linkIcon);
    }
    setValue(tmpAmount);
  }, [amount]);

  return (
    <div className="card-commission col-lg-12 my-3 mx-1">
      <div className="card-body">
        <div className="row">
          <div className="col-8">
            <div className="numbers">
              <p className="text-sm mb-0 text-uppercase font-weight-bold">
                {title} {title === 'COMMISSION' && '(4% win/lose)'}
              </p>
              <h5 className="font-weight-bolder">{value}</h5>
              <p className="mb-0">
                <Tooltip arrow placement="top" title={tooltip}>
                  <span
                    onClick={onClick}
                    className="text-success text-sm font-weight-bolder cursor-pointer"
                  >
                    {iconLink}
                    {linkText}
                  </span>
                </Tooltip>
              </p>
            </div>
          </div>
          <div
            className="col-4 flex"
            // onClick={onClick}
            style={{ justifyContent: 'end' }}
          >
            {/* <div className="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
              <i
                className="ni ni-money-coins text-lg opacity-10"
                aria-hidden="true"
              ></i>
            </div> */}
            {icon}
          </div>
        </div>
      </div>
    </div>
  );
};

export default CardCommission;
