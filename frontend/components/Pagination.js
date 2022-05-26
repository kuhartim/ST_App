import clsx from "clsx";
import { usePagination, DOTS } from "../hooks/usePagination";

import styles from "../styles/components/Pagination.module.scss";

export default function Pagination({
  onPageChange,
  maxPage,
  siblingCount,
  currentPage,
}) {
  const paginationRange = usePagination({
    currentPage,
    totalPageCount: maxPage,
    siblingCount,
  });

  // If there are less than 2 times in pagination range we shall not render the component
  if (currentPage === 0 || paginationRange.length < 2) {
    return null;
  }

  const onNext = () => {
    onPageChange(currentPage + 1);
  };

  const onPrevious = () => {
    onPageChange(currentPage - 1);
  };

  let lastPage = paginationRange[paginationRange.length - 1];
  return (
    <div className={styles["pagination"]}>
      {/* Left navigation arrow */}
      <button
        //disabled: currentPage === 1,
        className={styles["pagination__element"]}
        disabled={currentPage === 1}
        onClick={onPrevious}
      >
        &lt;
      </button>
      {paginationRange.map((pageNumber, i) => {
        // If the pageItem is a DOT, render the DOTS unicode character
        if (pageNumber === DOTS) {
          return (
            <span className={styles["pagination__element"]} key={i}>
              .&nbsp;.&nbsp;.
            </span>
          );
        }

        // Render our Page Pills
        return (
          <button
            //selected: pageNumber === currentPage,
            key={i}
            className={clsx(
              styles["pagination__element"],
              pageNumber === currentPage &&
                styles["pagination__element--selected"]
            )}
            onClick={() => onPageChange(pageNumber)}
          >
            {pageNumber}
          </button>
        );
      })}
      {/*  Right Navigation arrow */}
      <button
        //disabled: currentPage === lastPage,
        className={styles["pagination__element"]}
        disabled={currentPage === lastPage}
        onClick={onNext}
      >
        &gt;
      </button>
    </div>
  );
}
