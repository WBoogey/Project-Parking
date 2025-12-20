import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { ownerApi } from "@/api/ownerApi";
import { queryKeys } from "@/api/queryKeyFactory";
import { useNavigate } from "react-router";

export const useOwnerParkings = () => {
  return useQuery({
    queryKey: queryKeys.owner.parkings(),
    queryFn: ownerApi.getParkings,
  });
};

export const useAddParking = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  return useMutation({
    mutationFn: ownerApi.addParking,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.owner.parkings() });
      navigate("/owner");
    },
  });
};

export const useDeleteParking = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ownerApi.deleteParking,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.owner.parkings() });
    },
  });
};
