import { useNavigate } from "react-router";
import AuthForm from "@/components/organisms/AuthForm";
import { useLogin } from "@/hooks/useAuth";

export default function Login() {
  const navigate = useNavigate();
  const { mutate, isPending, error } = useLogin();

  return (
    <div className="flex items-center justify-center bg-gray-50 p-4">
      <AuthForm
        mode="login"
        onSubmit={(data) => mutate(data)}
        onNavigateToSignup={() => navigate("/register")}
        onBackToSite={() => navigate("/")}
        isLoading={isPending}
        error={error ? (error as Error).message : undefined}
      />
    </div>
  );
}
