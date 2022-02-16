import { Row, Col, Space, Skeleton } from "antd";
import Resources from "components/Resources";
import LicenseStatus from "components/LicenseStatus";
import QuickStats from "components/QuickStats";
import RecentWholesaleOrders from "components/RecentWholesaleOrders";
import TopWholesaleCustomers from "components/TopWholesaleCustomers";
import "antd/dist/antd.css";
import "./App.scss";

// Redux
import { dashboardActions } from "store/actions";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { useEffect } from "react";

const { fetchDashboardTexts, fetchDashboardData } = dashboardActions;

interface IAPP {
  fetching: boolean;
  wws_logo: string;
  logo_link: string;
  i18n: any;
  actions: any;
}

const App = (props: IAPP) => {
  const { actions, i18n, wws_logo, logo_link, fetching } = props;

  useEffect(() => {
    actions.fetchDashboardTexts();
    actions.fetchDashboardData();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <div className="wrap">
      {fetching ? (
        <Skeleton.Button
          style={{ width: 300, height: 100, marginTop: 20 }}
          active={true}
          size="large"
        />
      ) : (
        <a
          href={logo_link}
          target="_blank"
          rel="noreferrer"
          className="logo-link"
        >
          <img src={wws_logo} alt="Wholesale Suite Logo" className="logo" />
        </a>
      )}

      <h1>
        {fetching ? (
          <Skeleton.Button
            style={{ marginTop: "20px", width: 150, height: 50 }}
            active={true}
            size="large"
          />
        ) : (
          i18n?.dashboard
        )}
      </h1>
      <Row gutter={[24, 40]}>
        <Col span={16}>
          <QuickStats />
          <TopWholesaleCustomers />
          <RecentWholesaleOrders />
        </Col>
        <Col span={8}>
          <Space direction="vertical" size="large">
            <Resources />
            <LicenseStatus />
          </Space>
        </Col>
      </Row>
    </div>
  );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  wws_logo: store?.dashboard?.wws_logo,
  logo_link: store?.dashboard?.logo_link,
  i18n: store?.dashboard?.internationalization
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators(
    {
      fetchDashboardTexts,
      fetchDashboardData
    },
    dispatch
  )
});

export default connect(mapStateToProps, mapDispatchToProps)(App);
