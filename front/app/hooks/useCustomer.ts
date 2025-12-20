import { useQuery } from "@tanstack/react-query";
import { customerApi } from "@/api/customerApi";
import { queryKeys } from "@/api/queryKeyFactory";

export const useCustomerReservations = () => {
  return useQuery({
    queryKey: queryKeys.customer.reservations(),
    queryFn: customerApi.getReservations,
  });
};

export const useCustomerSubscriptions = () => {
  return useQuery({
    queryKey: queryKeys.customer.subscriptions(),
    queryFn: customerApi.getSubscriptions,
  });
};
