import { useMutation, useQueryClient } from "@tanstack/react-query";
import { reservationApi } from "@/api/reservationApi";
import { useNavigate } from "react-router";
import { queryKeys } from "@/api/queryKeyFactory";

export const useCreateReservation = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  return useMutation({
    mutationFn: reservationApi.createReservation,
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.reservations(),
      });
      navigate("/customer");
    },
  });
};
