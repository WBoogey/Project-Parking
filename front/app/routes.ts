import {
  type RouteConfig,
  index,
  route,
  layout,
} from "@react-router/dev/routes";

export default [
  layout("routes/layout.tsx", [
    index("routes/home.tsx"),
    route("search", "routes/search.tsx"),
    route("parking/:id", "routes/parking-details.tsx"),
    route("payment", "routes/payment.tsx"),

    route("owner", "routes/owner/dashboard.tsx"),
    route("owner/parkings/add", "routes/owner/add-parking.tsx"),

    route("customer", "routes/customer/dashboard.tsx"),
  ]),

  route("login", "routes/login.tsx"),
  route("register", "routes/register.tsx"),
  route("register/pro", "routes/register-pro.tsx"),
] satisfies RouteConfig;
