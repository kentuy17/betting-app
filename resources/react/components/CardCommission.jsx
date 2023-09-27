const CardCommission = ({ amount, title, linkText }) => {
  return (
    <div className="card-commission col-lg-12 my-3 mx-1">
      <div className="card-body">
        <div className="row">
          <div className="col-8">
            <div className="numbers">
              <p className="text-sm mb-0 text-uppercase font-weight-bold">
                {title}
              </p>
              <h5 className="font-weight-bolder">₱ {amount}</h5>
              <p className="mb-0">
                <span className="text-success text-sm font-weight-bolder">
                  {linkText}
                </span>
              </p>
            </div>
          </div>
          <div className="col-4 text-end">
            <div className="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
              <i
                className="ni ni-money-coins text-lg opacity-10"
                aria-hidden="true"
              ></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CardCommission;
