import { Link, useNavigate } from "react-router";
import Button from "../atoms/Button";
import LinkDropdown from "../molecules/LinkDropdown/LinkDropdown";
import { searchDropdownData, ownerDropdownData } from "./Navbar.data";
import { useUser } from "@/hooks/useUser";
import { useLogout } from "@/hooks/useAuth";

const Navbar = () => {
  const { data: user, isLoading } = useUser();
  const logoutMutation = useLogout();
  const navigate = useNavigate();

  const homeLink = user
    ? user.role === "owner"
      ? "/owner"
      : "/customer"
    : "/";

  const searchDropdownElements = [...searchDropdownData.elements];
  if (user && user.role === "customer") {
    const firstGroup = [...searchDropdownElements[0]];
    if (!firstGroup.find((el) => el.href === "/customer")) {
      firstGroup.push({ title: "Mes réservations", href: "/customer" });
    }
    searchDropdownElements[0] = firstGroup;
  }

  const ownerDropdownElements =
    user && user.role === "owner"
      ? ownerDropdownData.elements
      : [
          [
            { title: "Espace Propriétaire", href: "/register/pro" },
            { title: "Devenir partenaire", href: "/register/pro" },
          ],
        ];

  return (
    <nav className="flex items-center justify-between px-8 py-4 bg-primary border-b border-tertiary w-full">
      <Link to={homeLink} className="text-xl font-semibold">
        AppName
      </Link>
      <div className="flex items-center gap-8">
        <LinkDropdown
          title={searchDropdownData.title}
          dropdownElements={searchDropdownElements}
        />
        <LinkDropdown
          title={ownerDropdownData.title}
          dropdownElements={ownerDropdownElements}
        />
      </div>
      <div className="flex items-center gap-4">
        {isLoading ? (
          <div className="w-24 h-10 bg-gray-100 rounded-2xl animate-pulse" />
        ) : user ? (
          <>
            <Link
              to={user.role === "owner" ? "/owner" : "/customer"}
              className="text-secondary hover:underline font-medium"
            >
              {user.role === "owner" ? "Espace Pro" : "Mon Espace"}
            </Link>
            <Button
              onClick={() => logoutMutation.mutate()}
              size="sm"
              variant="outline"
              disabled={logoutMutation.isPending}
            >
              {logoutMutation.isPending ? "..." : "Déconnexion"}
            </Button>
          </>
        ) : (
          <>
            <Button onClick={() => navigate("/register")} variant="plain">
              S&apos;inscrire
            </Button>
            <Button onClick={() => navigate("/login")} size="sm">
              Se connecter
            </Button>
          </>
        )}
      </div>
    </nav>
  );
};

export default Navbar;

