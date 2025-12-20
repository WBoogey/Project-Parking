import { useQuery } from "@tanstack/react-query";
import { userApi } from "@/api/userApi";
import { queryKeys } from "@/api/queryKeyFactory";

export const useUser = () => {
  return useQuery({
    queryKey: queryKeys.auth.user(),
    queryFn: userApi.getMe,
    retry: false,
    staleTime: 1000 * 60 * 5,
  });
};
